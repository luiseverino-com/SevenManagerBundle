<?php
/**
 * Created by PhpStorm.
 * User: lseverino
 * Date: 03/11/15
 * Time: 23:20
 */

namespace SevenManagerBundle\DataFixtures\PHPCR;

use SevenManagerBundle\Document\Blocks\ImageOne;
use Symfony\Component\DependencyInjection\ContainerAware;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ODM\PHPCR\DocumentManager;
use SevenManagerBundle\Document\Containers\Slideshow;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class LoadSliderHomepage
 *
 * @package SevenManagerBundle\DataFixtures\PHPCR
 */
class LoadSliderHomepage extends ContainerAware implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * @return int
     */
    public function getOrder()
    {
        return 1;
    }

    /**
     * @param ObjectManager $objectManager
     */
    public function load(ObjectManager $objectManager)
    {
        global $kernel;
        $docRoot = $kernel->getRootDir();
        $publicResources = dirname(__FILE__) . '/../../Resources/public';

        if (!$objectManager instanceof DocumentManager) {
            $class = get_class($objectManager);
            throw new \RuntimeException("Fixture requires a PHPCR ODM DocumentManager instance, instance of '$class' given.");
        }

        // If Parent is null create one
        $thisParent = $objectManager->find(null, '/seven-manager/slideshow');
        if (!$thisParent) {
            $dm = $kernel->getContainer()->get('seven_manager.parent_manager');
            $dm->createRecursivePaths('/seven-manager/slideshow');
        }

        // Parent Document
        $parentPath = $objectManager->find(null, '/seven-manager/slideshow');

        // Child Document - create a new Page object
        $slideshow = new Slideshow();
        $image = new ImageOne();
        $upload = new UploadedFile($publicResources . '/img/slides/1.jpg', '1.jpg');
        $image->setName('ImageOne');

        // Add slideshow
        $slideshow->setName('home-slideshow');
        $slideshow->setParentDocument($parentPath);
        $slideshow->addChildren($image->setImage($upload));

        // Persist and flush
        $objectManager->persist($slideshow);
        //$objectManager->flush();
    }

    /**
     * @param DocumentManager $documentManager
     * @param                 $parent
     * @param                 $name
     * @param                 $title
     * @param                 $content
     *
     * @return Slideshow
     */
    protected function createSlideshow(DocumentManager $documentManager, $parent, $name, $title, $content)
    {
        $slideshow = new Slideshow();
        $slideshow->setTitle($title);
        $slideshow->setContent($content);
        $documentManager->persist($slideshow);
        $documentManager->flush();

        return $slideshow;
    }
}
