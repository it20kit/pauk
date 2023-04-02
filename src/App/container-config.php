<?php

return [
    \App\Systems\Game::class => new \App\Factory\GameFactory(),
    \App\Systems\GraphicSystem::class => new \App\Factory\GraphicSystemFactory(),
    \App\Systems\InputSystem::class => new \App\Factory\InputSystemFactory(),
];
