<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iceman
 * Date: 08.08.13
 * Time: 16:20
 * To change this template use File | Settings | File Templates.
 */

class User_Social extends fvRoot {

    static function getEntity(){
        return __CLASS__;
    }

    /** User */
    static function auth( $netUserId, $connect = false ){
        /** @var User_Social $socialUser */
        $socialUser = self::find( array( "netId" => $netUserId ) );

        fvSite::pdo()->beginTransaction();

        try{
            if( ! $socialUser instanceof static ){
                $socialUser = new static;
                $socialUser->netId = $netUserId;
                $socialUser->import();
                if( ! $socialUser->save() ){
                    throw new Exception( "Storage problems: cannot save user" );
                }
            }

            if( $connect ){
                $user = fvSite::session()->getUser();

                if( ! $user instanceof User ){
                    throw new Exception("You are not logged in");
                }

                if( $socialUser->userId->get() && $socialUser->userId->get() != $user->getId() ){
                    throw new AlreadyConnectedException("Already connect to other user");
                }

                $socialUser->userId = $user->getId();
                $socialUser->initiator = false;
                $socialUser->save();

                if( ! $user->name->get() )
                    $user->name = $socialUser->name->get();

                if( ! $user->surname->get() )
                    $user->surname = $socialUser->surname->get();

                if( ! $user->email->get() )
                    $user->email = $socialUser->email->get();

                if( ! $user->image->get() )
                    $user->image = $socialUser->image->get();

                $user->save();
            } else {
                /** @var User $user */
                $user = User::select()
                    ->useQModifiers(false)
                    ->where(array( "id" => $socialUser->userId->get() ))
                    ->fetchOne();

                if( ! $user instanceof User ){
                    $user = new User( array(
                        "name" => $socialUser->name->get(),
                        "surname" => $socialUser->surname->get(),
                        "image" => $socialUser->image->get(),
                        "email" => $socialUser->email->get()
                    ));
                    $user->save();
                    $socialUser->userId = $user->getId();
                    $socialUser->initiator = true;
                    $socialUser->save();
                }

                if( ! $user->isActive->get() ){
                    throw new Exception( "Access revoked by administrator." );
                }

                $user->signIn();
            }

            fvSite::pdo()->commit();
        } catch( Exception $e ){
            fvSite::pdo()->rollback();
            throw $e;
        }

        return $user;
    }

    /**
     * @return User
     */
    public function import(){
       throw new Exception("not implemented");
    }

    function toJSON(){
        $data = parent::toJSON();
        $data["subclass"] = get_class( $this );
        $data["netId"] = $this->netId->get();

        return $data;
    }


}