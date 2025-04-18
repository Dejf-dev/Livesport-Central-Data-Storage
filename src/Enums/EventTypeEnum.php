<?php

namespace App\Enums;

/**
 * Event type describing what type of event happened during match
 *
 * @package App\Enums
 */
enum EventTypeEnum: string {
    case GOAL = "goal";
    case YELLOW_CARD = "yellow_card";
    case RED_CARD = "red_card";
    case OWN_GOAL = "own_goal";
    case SUBSTITUTION_IN = "substitution_in";
    case SUBSTITUTION_OUT = "substitution_out";
    case PENALTY_GOAL = "penalty_goal";
    case PENALTY_MISS = "penalty_miss";
}