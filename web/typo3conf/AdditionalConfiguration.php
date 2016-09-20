<?php declare(strict_types = 1);

/*
 * This file is part of the Bartacus Standard Edition.
 *
 * The Bartacus Standard Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * The Bartacus Standard Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with the Bartacus Standard Edition.  If not, see <http://www.gnu.org/licenses/>.
 */

use Bartacus\Bundle\BartacusBundle\Config\ConfigLoader;
use Symfony\Component\HttpKernel\Kernel;

$kernel = $GLOBALS['kernel'];
if ($kernel instanceof Kernel) {
    /** @var ConfigLoader $configLoader */
    $configLoader = $kernel->getContainer()->get('bartacus.config_loader');
    $configLoader->loadFromAdditionalConfiguration();
}