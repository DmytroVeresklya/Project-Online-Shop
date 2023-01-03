<?php

namespace App\DataFixtures;

interface FixtureDataAwareInterface
{
    /**
     * @return array
     */
    public function getFixturesData(): array;
}
