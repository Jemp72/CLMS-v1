<?php

it('can render', function () {
    $contents = $this->view('loggingterminal', [
        //
    ]);

    $contents->assertSee('');
});
