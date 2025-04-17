<?php

namespace App\Constants;

class ErrorMessages
{
    public const TEAM_NOT_FOUND = 'Team by inputted id not found!';
    public const MATCH_NOT_FOUND = 'Football match by inputted id not found!';
    public const EVENT_NOT_FOUND = 'Event by inputted id not found!';
    public const EVENT_TEAM_NOT_RELATED = "Team in event request is not occurred in match's teams";
}