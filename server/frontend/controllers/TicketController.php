<?php

namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\widgets;

/**
 * index controller
 */
class TicketController extends Controller {
    
    public function actionUserList(){
        
        $page = (int) Yii::$app->request->post("p");
        $page = $page > 1 ? $page - 1 : 0;
        
        $uid = widgets\User::getUid();
        $list = \common\models\user\UserTicket::getUserList($uid,$page);
        
        $this->asJson(widgets\Response::sucess($list));
    }
    
    public function actionAdd(){
        
        $ticketId = (int) Yii::$app->request->post("ticket_id");
        if (!$ticketId) {
            $this->asJson(widgets\Response::error("优惠券id不为空"));
            return;
        }

        $ticket = \common\models\comm\CommTicket::findOne($ticketId);
        if (!$ticket) {
            $this->asJson(widgets\Response::error("优惠券错误"));
            return;
        }
        
        if (!$ticket->status) {
            $this->asJson(widgets\Response::error("优惠券已停发"));
            return;
        }
        
        if ($ticket->num >= $ticket->count) {
            $this->asJson(widgets\Response::error("优惠券已送完"));
            return;
        }
        
        $uid = widgets\User::getUid();
        $userTicketModel = new \common\models\user\UserTicket();
        $userTicketModel->user_id = $uid;
        $userTicketModel->ticket_id = $ticketId;
        $userTicketModel->money = $ticket->money;
        $userTicketModel->status = 1;
        $userTicketModel->end_time = time() + $ticket->duration;
        if ($userTicketModel->save()){
            $this->asJson(widgets\Response::sucess([]));
        }
        
        $this->asJson(widgets\Response::error('领取失败'));
    }
}