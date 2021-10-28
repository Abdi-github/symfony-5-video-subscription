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
        foreach ($this->getVideosData() as [$name, $category_id, $path, $director, $cast, $image, $country, $created_at]) {
            $category = $manager->getRepository(Category::class)->find($category_id);
            // $genre = $manager->getRepository(Genre::class)->find($genre);
            $duration = random_int(40, 150);

            $rating = mt_rand(5.5 * 10, 8.5 * 10) / 10;
            $release_year = \random_int(1990, 2021);






            $video = new Video();

            $video->setName($name);
            $video->setCategory($category);
            // $video->addGenre($genre);
            $video->setDuration($duration);
            $video->setPath('https://player.vimeo.com/video/' . $path);
            $video->setReleaseYear($release_year);
            $video->setDirector($director);
            $video->setCast($cast);
            $video->setImage($image);
            $video->setRating($rating);
            $video->setCountry($country);

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
            ['Movie 1', 1,  289729765, 'Mark Wayne', ['Dayna Villanueva', 'Russell Golden', 'Giorgio Cunningham', 'Marguerite Perry'], 'cover.jpg', 'USA', '2021-09-01 12:34:45'],
            ['Movie 2', 1,  238902809, 'Ava-May Valenzuela', ['Lemar Mitchell', 'Briana Walton', 'Henley Casey', 'Ravinder Walsh'], 'cover18.jpg', 'USA', '2021-10-01 12:34:45'],
            ['Movie 3', 1,  150870038, 'Rebecca Coleman', ['Gregg Khan', 'Heidi Finnegan', 'Nieve Weeks', 'Bernard Rios'],  'cover2.jpg', 'UK', '2021-09-06 12:34:45'],
            ['Movie 4', 1,  219727723, 'Abdi Ahmed', ['Lacey-Mai Watkins', 'Kylan Hassan', 'Taine Pratt', 'Ana Glass'], 'cover3.jpg', 'USA', '2021-09-07 12:34:45'],
            ['Movie 5', 1,  289879647, 'Anis Hail', ['Leyton Ahmad', 'Gilbert Maddox', 'Dayna Villanueva', 'Jayne Webb'], 'cover4.jpg', 'France', '2021-09-01 12:34:45'],
            ['Movie 6', 1,  261379936, 'Kurt Ferreira', ['Russell Golden', 'Nayan Britt', 'Maci Carpenter', 'Loretta Bass'], 'cover5.jpg', 'India', '2021-10-02 12:34:45'],
            ['Movie 7', 1,  289029793, 'Solomon Harrison', ['Benn Charles', 'Chloe-Ann Burn', 'Elijah Mccoy', 'Georgiana Evans'], 'cover6.jpg', 'USA', '2021-09-30 12:34:45'],
            ['Movie 8', 1,  60594348, 'Jovan Ramsay', ['Marguerite Perry', 'Rita Griffin', 'Abbey Cresswell', 'Graham Noble'], 'cover7.jpg', 'South Korea', '2021-09-02 12:34:45'],
            ['Movie 9', 1,  290253648, 'Sapphire Wu', ['Piotr Bray', 'Gregg Khan', 'Reef Senior', 'Dayna Villanueva'], 'cover8.jpg', 'China', '2021-09-09 12:34:45'],

            ['Serie 1', 2,  289729765, 'Catrin Jimenez', ['Elvis Maxwell', 'Willard Power', 'Elijah Mccoy', 'Ana Glass'], 'cover9.jpg', 'USA', '2021-09-10 12:34:45'],
            ['Serie 2', 2,  289729765, 'Tommy Watkins', ['Kirstie Hooper', 'Mildred Whitaker', 'Drake Byrne', 'Nate Boyd'], 'cover10.jpg', 'UK', '2021-09-04 12:34:45'],
            ['Serie 3', 2,  289729765, 'Mikael Valencia', ['Kitty Akhtar', 'Dayna Villanueva', 'Briana Walton', 'Ravinder Walsh'], 'cover11.jpg', 'France', '2021-10-02 12:34:45'],
            ['Serie 4', 2,  289729765, 'Arielle Espinoza', ['Maximillian Andrews', 'Dayna Villanueva', 'Dayna Villanueva', 'Dayna Villanueva'], 'cover12.jpg', 'Spain', '2021-09-30 12:34:45'],
            ['Serie 5', 2,  289729765, 'Henna Haas', ['Heidi Finnegan', 'Leyton Ahmad', 'Dayna Villanueva', 'Nieve Weeks'], 'cover13.jpg', 'USA', '2021-10-01 12:34:45'],
            ['Serie 6', 2,  289729765, 'Mahima Wickens', ['Kylan Hassan', 'Christina Haines', 'Bernard Rios', 'Mcauley Cantu'], 'cover14.jpg', 'South Korea', '2021-09-20 12:34:45'],

            ['Documenatry 1', 3,  289729765, 'Mahima Wickens', ['Shanay Lowry', 'Cleveland Oneil', 'Montel Davis', 'Justin Quinn'], 'cover15.jpg', 'USA', '2021-09-27 12:34:45'],
            ['Documenatry 2', 3,  289729765, 'Lucia Cottrell', ['Sia Fry', 'Maddox Redfern', 'Kenny Hays', 'Gregg Terrell'], 'cover16.jpg', 'USA', '2021-09-28 12:34:45'],
            ['Documenatry 3', 3,  289729765, 'Yvonne Ferrell', ['Gareth Dorsey', 'Gruffydd Lim', 'Kay Pugh', 'Lacey-Mai Watkins'], 'cover17.jpg', 'UK', '2021-09-29 12:34:45'],
            ['Documenatry 4', 3,  289729765, 'Vincent Palmer', ['Ivy-Rose Wardle', 'Taine Pratt', 'Robin Torres', 'Lemar Mitchell'], 'cover18.jpg', 'Mexico', '2021-09-30 12:36:45'],
            ['Documenatry 5', 3,  289729765, 'Elsie-Mae Knight', ['Silas Christie', 'Tonisha Lam', 'Loretta Bass', 'Carwyn North'], 'cover.jpg', 'USA', '2021-09-14 12:34:45'],
            ['Documenatry 6', 3,  289729765, 'Dayna Villanueva', ['Janice Humphries', 'Arnold Piper', 'Romany Hayden', 'Joanne Holman'], 'cover18.jpg', 'USA', '2021-09-15 12:34:45'],





        ];
    }


    private function videoGenreData()
    {
        return [

            [1, 1],
            [1, 2],

            [2, 1],
            [2, 3],

            [3, 1],

            [4, 2],

            [5, 1],
            [5, 2],

            [6, 3],
            [6, 1],

            [7, 2],
            [8, 2],
            [9, 3],

            [10, 1],
            [10, 2],

            [11, 2],
            [12, 1],
            [13, 3],
            [14, 1],
            [14, 3],
            [15, 2],

            [16, 4],
            [17, 5],
            [18, 4],
            [19, 6],
            [20, 4],
            [21, 6]


        ];
    }
}
