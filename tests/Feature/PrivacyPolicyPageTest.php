<?php

it('publishes the privacy policy page and links to it from the public footer', function () {
    $this->get(route('privacy.policy'))
        ->assertOk()
        ->assertSee('Règles de confidentialité et données personnelles')
        ->assertSee('contactbisika@gmail.com')
        ->assertSee(route('privacy.policy'));
});
