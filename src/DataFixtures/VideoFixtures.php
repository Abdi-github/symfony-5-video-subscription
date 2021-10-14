<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;
use App\Entity\Genre;
use App\Entity\Video;
use DateTime;

// use Monolog\DateTimeImmutable;
// use Symfony\Component\Validator\Constraints\DateTime;

class VideoFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        foreach ($this->getVideosData() as [$name, $category_id, $path, $image, $created_at]) {
            $category = $manager->getRepository(Category::class)->find($category_id);
            // $genre = $manager->getRepository(Genre::class)->find($genre);
            $duration = random_int(40, 150);

            $rating = mt_rand(5.5 * 10, 8.5 * 10) / 10;






            $video = new Video();

            $video->setName($name);
            $video->setCategory($category);
            // $video->addGenre($genre);
            $video->setDuration($duration);
            $video->setPath('https://player.vimeo.com/video/' . $path);
            $video->setImage($image);
            $video->setRating($rating);
            $video->setCreatedAtForFixtures(new DateTime($created_at));



            $manager->persist($video);
        }

        $manager->flush();
        $this->loadVideo_Genre($manager);
    }

    public function loadVideo_Genre($manager)
    {
        foreach ($this->videoGenreData() as [$video_id, $genre_id]) {

            $video = $manager->getRepository(Video::class)->find($video_id);
            $genre = $manager->getRepository(Genre::class)->find($genre_id);

            $video->addGenre($genre);
            $manager->persist($video);
        }

        $manager->flush();
    }

    private function getVideosData()
    {
        return [
            ['Movie 1', 1,  289729765, 'cover.jpg', '2021-09-01 12:34:45'],
            ['Movie 2', 1,  238902809, 'cover18.jpg', '2021-10-01 12:34:45'],
            ['Movie 3', 1,  150870038, 'cover2.jpg', '2021-09-06 12:34:45'],
            ['Movie 4', 1,  219727723, 'cover3.jpg', '2021-09-07 12:34:45'],
            ['Movie 5', 1,  289879647, 'cover4.jpg', '2021-09-01 12:34:45'],
            ['Movie 6', 1,  261379936, 'cover5.jpg', '2021-10-02 12:34:45'],
            ['Movie 7', 1,  289029793, 'cover6.jpg', '2021-09-30 12:34:45'],
            ['Movie 8', 1,  60594348, 'cover7.jpg', '2021-09-02 12:34:45'],
            ['Movie 9', 1,  290253648, 'cover8.jpg', '2021-09-09 12:34:45'],

            ['Serie 1', 2,  289729765, 'cover9.jpg', '2021-09-10 12:34:45'],
            ['Serie 2', 2,  289729765, 'cover10.jpg', '2021-09-04 12:34:45'],
            ['Serie 3', 2,  289729765, 'cover11.jpg', '2021-10-02 12:34:45'],
            ['Serie 4', 2,  289729765, 'cover12.jpg', '2021-09-30 12:34:45'],
            ['Serie 5', 2,  289729765, 'cover13.jpg', '2021-10-01 12:34:45'],
            ['Serie 6', 2,  289729765, 'cover14.jpg', '2021-09-20 12:34:45'],

            ['Documenatry 1', 3,  289729765, 'cover15.jpg', '2021-09-27 12:34:45'],
            ['Documenatry 2', 3,  289729765, 'cover16.jpg', '2021-09-28 12:34:45'],
            ['Documenatry 3', 3,  289729765, 'cover17.jpg', '2021-09-29 12:34:45'],
            ['Documenatry 4', 3,  289729765, 'cover18.jpg', '2021-09-30 12:36:45'],
            ['Documenatry 5', 3,  289729765, 'cover.jpg', '2021-09-14 12:34:45'],
            ['Documenatry 6', 3,  289729765, 'cover18.jpg', '2021-09-15 12:34:45'],





        ];
    }


    private function videoGenreData()
    {
        return [

            [12, 1],
            [12, 2],
            [12, 3],

            [11, 1],
            [11, 2],

            [1, 1],
            [1, 2],
            [1, 3],

            [2, 1],
            [2, 2]

        ];
    }
}
