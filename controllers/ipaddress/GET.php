<?php

return function (array &$context) {
    return [
        "address" => $_SERVER['REMOTE_ADDR'],
    ];
};
