<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Video;
use App\Repository\CommentRepository;
use App\Repository\VideoRepository;
use Doctrine\DBAL\Schema\View;
use Gedmo\Sluggable\Sluggable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VideoDetailController extends AbstractController
{
    #[Route('/video/detail/{video_id}', name: 'video_detail')]
    public function index(Request $request, VideoRepository $vr): Response

    {
        // \dd($request->attributes->get('video_id'));
        $video_id = $request->attributes->get('video_id');
        // \dd($video_id);

        $video = $vr->findOneBy(['id' => $video_id]);


        // $video = $vr->getVideoDetails($video_id);



        // \dd($video);


        return $this->render('video_detail/index.html.twig', [
            'video' => $video
        ]);
    }

    #[Route('/add/comment/{video}', name: 'add.comment')]
    public function addComment(Request $request, Video $video): Response
    {
        // \dd($video);

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        $submittedToken = $request->query->get('token');
        $submittedComment = \trim($request->query->get('comment'));
        $currentUser = $this->getUser();

        // \dd($currentUser);

        if (!empty($submittedComment)  && $this->isCsrfTokenValid('add-comment', $submittedToken)) {
            $comment = new Comment();

            $comment->setContent($submittedComment);
            $comment->setVideo($video);
            $comment->setUser($currentUser);

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
        }

        return $this->redirectToRoute('video_detail', [
            'video_id' => $video->getId(),
        ]);
    }

    #[Route('/add/comment/like/{comment}', name: 'add.like.comment')]
    public function addLike(Request $request, Comment $comment, CommentRepository $cr)
    {
        // \dd($comment);
        // \dd($this->getUser());

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $currentUserId = $this->getUser()->getId();
        // \dd($currentUserId);

        $commentDetail = $cr->getVideoDetails($comment->getId());
        $usersLikeTheComment = $commentDetail->getUsersLikeComments()->getValues();
        $usersDisLikeTheComment = $commentDetail->getUsersDislikeComments()->getValues();
        $numberOfLikes = \count($usersLikeTheComment);
        $numberOfDisLikes = \count($usersDisLikeTheComment);

        // UNDO

        if ($usersDisLikeTheComment) {
            foreach ($usersDisLikeTheComment as $dl) {
                $usersDisLikeId[] = $dl->getId();
            }

            if (in_array($currentUserId, $usersDisLikeId)) {
                $user = $this->getUser();
                $user->removeDislikedComment($comment);
                $user->addLikedComment($comment);
                // \dd($user);

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                return $this->json(['message' => 'undo disliked', 'likes' => $numberOfLikes + 1, 'dislikes' => $numberOfDisLikes - 1, 'commentId' => $comment->getId()]);
            }
        }


        if ($numberOfLikes > 0) {

            foreach ($usersLikeTheComment as $ul) {
                $usersLikeId[] = $ul->getId();
            }


            if (!in_array($currentUserId, $usersLikeId)) {
                $user = $this->getUser();

                $user->addLikedComment($comment);

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();


                return $this->json(['message' => 'liked', 'likes' => $numberOfLikes + 1, 'dislikes' => $numberOfDisLikes, 'commentId' => $comment->getId()]);;
            } else {
                return $this->json((['message' => 'already liked', 'likes' => $numberOfLikes, 'dislikes' => $numberOfDisLikes]));
            }
        } else {
            $user = $this->getUser();
            $user->addLikedComment($comment);
            // \dd($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->json(['message' => 'liked', 'likes' => $numberOfLikes + 1, 'dislikes' => $numberOfDisLikes, 'commentId' => $comment->getId()]);
        }
    }

    #[Route('/add/comment/dislike/{comment}', name: 'add.dislike.comment')]
    public function addDislike(Request $request, Comment $comment, CommentRepository $cr)
    {
        // \dd($comment);
        // \dd($this->getUser());

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $currentUserId = $this->getUser()->getId();
        // \dd($currentUserId);

        $commentDetail = $cr->getVideoDetails($comment->getId());
        $usersDisLikeTheComment = $commentDetail->getUsersDislikeComments()->getValues();
        $usersLikeTheComment = $commentDetail->getUsersLikeComments()->getValues();
        $numberOfDisLikes = \count($usersDisLikeTheComment);
        $numberOfLikes = \count($usersLikeTheComment);

        // UNDO

        if ($usersLikeTheComment) {
            foreach ($usersLikeTheComment as $ul) {
                $usersLikeId[] = $ul->getId();
            }

            if (in_array($currentUserId, $usersLikeId)) {
                $user = $this->getUser();
                $user->removeLikedComment($comment);
                $user->addDislikedComment($comment);
                // \dd($user);

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                return $this->json(['message' => 'undo liked', 'dislikes' => $numberOfDisLikes + 1, 'likes' => $numberOfLikes - 1, 'commentId' => $comment->getId()]);
            }
        }


        if ($numberOfDisLikes > 0) {

            foreach ($usersDisLikeTheComment as $dl) {
                $usersDisLikeId[] = $dl->getId();
            }


            if (!in_array($currentUserId, $usersDisLikeId)) {
                $user = $this->getUser();

                $user->addDislikedComment($comment);

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();


                return $this->json(['message' => 'disliked', 'dislikes' => $numberOfDisLikes + 1, 'likes' => $numberOfLikes, 'commentId' => $comment->getId()]);;
            } else {
                return $this->json((['message' => 'already disliked', 'dislikes' => $numberOfDisLikes, 'likes' => $numberOfLikes]));
            }
        } else {
            $user = $this->getUser();
            $user->addDislikedComment($comment);
            // \dd($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->json(['message' => 'disliked', 'dislikes' => $numberOfDisLikes + 1, 'likes' => $numberOfLikes, 'commentId' => $comment->getId()]);
        }
    }
}
