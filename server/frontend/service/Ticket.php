<?php
namespace frontend\service;
use \yii\db\Exception as Exception;


class Ticket {
    
    public function getIndex($uid){
        $list = \common\models\comm\CommTicket::find()->where(['index_show' => 1])->andWhere(['status' => 1])->orderBy("id desc")->limit(2)->all();
        if (!$list){
            return [];
        }
        
        $out = $ticketId = [];
        foreach ($list as $k =>$val){
            $val = $val->toArray();
            $ticketId[] = $val['id'];
            $arr['id'] = $val['id'];
            $arr['description'] = sprintf("满%d使用", $val['condition'] / 100);
            $arr['money'] = $val['money'] / 100;
            $arr['get'] = 0;
            $out[$val['id']] = $arr;
        }


        $userTickets = \common\models\user\UserTicket::find()->where(["user_id" => $uid])->andWhere(['in', "ticket_id", $ticketId])->all();
        $userTickets = is_array($userTickets) ? $userTickets : [];
        foreach ($userTickets as $val){
            $val = $val->toArray();
            $out[$val['ticket_id']]['get'] = 1;
        }

       
        return array_values($out);
    }
    
    public function getUserTicketId($uid, $id){
        
        $endTime = date("Y-m-d H:i:s");
        $sql = "select a.id, a.ticket_id,a.money, b.title, b.condition from user_ticket a
                inner JOIN comm_ticket b on a.ticket_id = b.id
                where a.id = {$id} and a.user_id = {$uid} and b.status=1 and a.end_time >= '$endTime'";
                
        $info = \common\models\user\UserTicket::findBySql($sql)->asArray()->one(); 
        if (!$info){
            return [];
        }
       
        return $info;
    }
    
    public function subTicket($userId, $ticketId, $price, $orderId){
        
        $uTicket = \common\models\user\UserTicket::getByUser($userId, $ticketId);
        if (!$uTicket){
            return 0;
        }
        
        if (!$uTicket->status){
            return 0;
        }
       
        $tieket = \common\models\comm\CommTicket::findOne($uTicket->ticket_id);
        if (!$tieket){
            return 0;
        }
        
        if ($tieket->duration < date("Y-m-d H:i:s")){
            return 0;
        }
        
        $status = $this->checkTicket($tieket, $price);
        if ($status){
            return 0;
        }
        
        $uTicket->status = 0;
        if (!$uTicket->save()){
            throw new Exception("优惠券使用失败");
        }
        
        $ticketLogModel = new \common\models\user\UserTicketLog();
        $ticketLogModel->user_id = $userId;
        $ticketLogModel->ticket_id = $ticketId;
        $ticketLogModel->order_id = $orderId;
        $ticketLogModel->money = $uTicket->money;
        $ticketLogModel->save();
        if (!$uTicket->save()){
            throw new Exception("优惠券使用失败");
        }
        
        return $uTicket->money;
    }
    
    /**
     * 
     * @param type $ticketId
     * @param array $product
     */
    public function checkTicket($ticket, $price){
        
        if ($ticket['condition'] >= $price){
            return true;
        }
        
        return false;
    }
}