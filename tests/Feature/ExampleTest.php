<?php

test('the application returns a successful response for guest on login', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});
