<?php

namespace Dnw\Game\Domain\Game\ValueObject\Phase;

enum PhaseTypeEnum: string
{
    case MOVEMENT = 'M';
    case RETREAT = 'R';
    case ADJUSTMENT = 'A';
    case NON_PLAYING = '-';

}
