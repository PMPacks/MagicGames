<?php

declare(strict_types=1);

namespace cosmicnebula200\MagicTags\queries;

interface Queries
{
    const CREATE_DB = 'magictags.init';
    const LOAD_DB = 'magictags.load';
    const CREATE_PLAYER = 'magictags.create';
    const UPDATE_PLAYER = 'magictags.update';
}
