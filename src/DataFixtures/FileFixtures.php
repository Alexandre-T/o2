<?php
/**
 * This file is part of the O2 Application.
 *
 * PHP version 7.1|7.2|7.3|7.4
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * @author    Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @copyright 2019 Alexandre Tranchant
 * @license   Cecill-B http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 */

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\File;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use Symfony\Component\HttpFoundation\File\File as HttpFile;

/**
 * File fixtures.
 */
class FileFixtures extends Fixture
{
    /**
     * Load files.
     *
     * @param ObjectManager $manager manager to save data
     *
     * @throws Exception returned by DateTimeImmutable
     */
    public function load(ObjectManager $manager): void
    {
        if (in_array(getenv('APP_ENV'), ['dev', 'test'])) {
            foreach (range(1, 40) as $index) {
                $file = new File();

                $file->setName('upload.txt');
                $file->setMimeType('application/txt');
                $file->setOriginalName('upload.txt');
                $file->setSize(1024);
                $file->setFile(new HttpFile(__DIR__.'/../../tests/_data/upload.txt'));
                $this->addReference('file'.$index, $file);

                $manager->persist($file);
            }
        }

        $manager->flush();
    }
}
