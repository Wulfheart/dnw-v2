<?php

namespace Dnw\Game\Application;

enum PermissionEnum: string
{
    case CREATE_GAME = 'game.create';
    case JOIN_GAME = 'game.join';
}
