<?php

declare(strict_types=1);

test('guests are redirected to the login screen', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('login'));
});
