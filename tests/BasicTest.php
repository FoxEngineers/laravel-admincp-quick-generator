<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FoxEngineers\AdminCP\Tests;

/**
 * @internal
 *
 * @coversNothing
 */
final class BasicTest extends BaseTestCase
{
    public function testBasic(): void
    {
        self::assertTrue(true);
        self::assertTrue(true);
        if(1===2){
            self::assertTrue(true);
        }
    }
}
