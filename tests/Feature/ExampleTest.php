<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * La racine du site redirige vers le back office.
     */
    public function test_the_root_redirects_to_the_admin_area(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/admin');
    }
}
