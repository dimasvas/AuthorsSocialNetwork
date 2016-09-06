<?php

namespace AppBundle\Service;

use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * Description of PagerToArrayAdapter
 *
 * @author dimas
 */
class ObjectToArrayAdapter {
    
    private $router;
    
    public function __construct(Router $router)
    {
        $this->router = $router;
       
    }
    
    public function commentsCollectionToArray($collection, $composition_id) 
    {
        $data = [];
        
        foreach ($collection as $comment) {
            $data[] = $this->commentObjectParser($comment, $composition_id);
        }
        
        return $data;
    }
    
    public function commentEntityToArray($comment, $composition_id) 
    {
        $data = [];
        
        $data[] = $this->commentObjectParser($comment, $composition_id);
        
        return $data;
    }
    
    public function latestCommentsToArray($collection){
        
        $data = [];
        //TODO: FIX PERFORMANCE
        foreach($collection as $comment){
            
         $data[] = [
                'id' => $comment->getId(),
                'content' => $comment->getContent(),
                'created' => $comment->getCreated(),
                'totalVote' => $comment->getTotalVote(),
                'owner' => [
                    'id' => $comment->getUser()->getId(),
                    'username' => $comment->getUser()->getUsername(),
                    'photo' => $comment->getUser()->getImageName(),
                    'profile_link' => $comment->getUser()->getIsAuthor() ? 
                                      $this->router->getGenerator()->generate('author_show', ['id' => $comment->getUser()->getId()]) :
                                      $this->router->getGenerator()->generate('fos_user_profile_show')
                ],
                'composition' => [
                    'id' => $comment->getComposition()->getId(),
                    'title' => $comment->getComposition()->getTitle(),
                ]
              ];
        }
        
        return $data;
    }
    
    private function commentObjectParser($comment, $composition_id) 
    {
        //FIXME
        /*TODO Oптимизировать запрос. Достать id всех пользователей. Достать их одним запросом и отсортировать*/
        /*TODO Оптимищировать вместо всего роутера передать только нужный метод.*/
        return [
              'id' => $comment->getId(),
              'user' => [
                  'id' => $comment->getUser()->getId(),
                  'username' => $comment->getUser()->getUsername(),
                  'photo' => $comment->getUser()->getImageName(),
                  'profile_link' => $comment->getUser()->getIsAuthor() ? 
                                    $this->router->getGenerator()->generate('author_show', ['id' => $comment->getUser()->getId()]) :
                                    $this->router->getGenerator()->generate('fos_user_profile_show')
              ],
              'content' => $comment->getContent(),
              'totalVote' => $comment->getTotalVote(),
              'has_response' => $comment->getHasResponse(),
              'responses_count' => $comment->getResponseCount(),
              'get_responses_url' => $this->router->getGenerator()
                                        ->generate('subcomment_list', [
                                                            'composition' => $composition_id, 
                                                            'comment_id' => $comment->getId()
                                                        ]
                                                ),  
              'created' => $comment->getCreated()
            ];
    }

//  private function commentObjectParser($comment, $composition_id, $subcomment_id = false) 
//    {
//        //FIXME
//        /*TODO Oптимизировать запрос. Достать id всех пользователей. Достать их одним запросом и отсортировать*/
//        /*TODO Оптимищировать вместо всего роутера передать только нужный метод.*/
//        $collection = [
//              'id' => $comment->getId(),
//              'user' => [
//                  'id' => $comment->getUser()->getId(),
//                  'username' => $comment->getUser()->getUsername(),
//                  'photo' => $comment->getUser()->getImageFile(),
//                  'profile_link' => $comment->getUser()->getIsAuthor() ? 
//                                    $this->router->getGenerator()->generate('author_show', ['id' => $comment->getUser()->getId()]) :
//                                    $this->router->getGenerator()->generate('profile')
//              ],
//              'content' => $comment->getContent(),
//              'has_response' => $comment->getHasResponse(),
//              'responses_count' => $comment->getResponseCount(), 
//              'created' => $comment->getCreated()
//            ];
//        
//        if(!$subcomment_id) {
//            $collection['get_responses_url'] = $this->router->getGenerator()->generate('subcomment_list', 
//                                                [
//                                                    'composition_id' => $composition_id, 
//                                                    'comment_id' => $comment->getId()
//                                                ]);
//        } else {
//            $collection['get_responses_url'] = $this->router->getGenerator()->generate('subcomment_more_list', 
//                                      [
//                                          'composition_id' => $composition_id, 
//                                          'comment_id' => $comment->getId(),
//                                          'subcomment_id' => $subcomment_id
//                                      ]);  
//        }
//        
//        return $collection;
//    }
}