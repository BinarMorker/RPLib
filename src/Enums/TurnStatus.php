<?php

namespace RPLib\Enums;

/**
 * Class TurnStatus
 * @package RPLib\Enums
 */
class TurnStatus {

    /**
     * Defines a turn that has not yet been started
     */
    const NOT_STARTED = 0;

    /**
     * Defines an ongoing turned that has never been over
     */
    const ONGOING = 1;

    /**
     * Defines a turn that is over
     */
    const FINISHED = 2;

    /**
     * Defines a turn that has been skipped
     */
    const SKIPPED = 3;

    /**
     * Defines a turn being paused to return to a previous turn
     */
    const PAUSED = 4;

    /**
     * Defines an ongoing turn that has been finished before
     */
    const FINISHED_THEN_RETURNED = 5;

    /**
     * Defines an ongoing turn that has been skipped before
     */
    const SKIPPED_THEN_RETURNED = 6;

    /**
     * Defines an ongoing turn that has been paused before
     */
    const PAUSED_THEN_RETURNED = 7;

    /**
     * Defines a turn that is over after being returned
     */
    const RETURNED_THEN_FINISHED = 8;

    /**
     * Defines a turn that is skipped after being returned
     */
    const RETURNED_THEN_SKIPPED = 9;

    /**
     * Defines a turn that is paused after being returned
     */
    const RETURNED_THEN_PAUSED = 10;

}