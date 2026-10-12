<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Ada Example',
            'email' => 'ada@example.test',
            'subject' => 'Portfolio enquiry',
            'message' => 'I would like to discuss a project with you.',
            'website' => '',
        ], $overrides);
    }

    public function test_valid_contact_message_is_persisted_with_success_feedback(): void
    {
        $this->from(route('home'))->post(route('contact.store'), $this->payload())
            ->assertRedirect(route('home').'#contact')
            ->assertSessionHas('contact_success');

        $this->assertDatabaseHas('contact_messages', ['email' => 'ada@example.test', 'subject' => 'Portfolio enquiry']);
    }

    public function test_contact_fields_and_honeypot_are_validated(): void
    {
        $this->post(route('contact.store'), $this->payload([
            'name' => '',
            'email' => 'bad',
            'subject' => str_repeat('x', 151),
            'message' => 'short',
            'website' => 'spam.example',
        ]))->assertSessionHasErrors(['name', 'email', 'subject', 'message', 'website']);

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_contact_form_is_rate_limited_per_visitor_identity(): void
    {
        foreach (range(1, 3) as $attempt) {
            $this->post(route('contact.store'), $this->payload())->assertRedirect();
        }

        $this->post(route('contact.store'), $this->payload())->assertTooManyRequests();
        $this->post(route('contact.store'), $this->payload(['email' => 'other@example.test']))->assertRedirect();
    }
}
