<?php

return function (array $context) {
    $context["renderer"]([
        "address" => $_SERVER['REMOTE_ADDR'],
    ]);
};
