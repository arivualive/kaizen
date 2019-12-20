<?php
class Admin
{
    private $admin_id;
    private $Curl;

    public function __construct(Curl $curl)
    {
        $this->Curl = $curl;
    }

    public function setAdminId($admin_id)
    {
        $this->admin_id = $admin_id;
    }

    public function getAdminId()
    {
        return $this->admin_id;
    }

    public function findAdminId()
    {
        $curl = array(
            'repository' => 'AdminRepository'
            , 'method' => 'findAdminId'
            , 'params' => array(
                'id' => $this->admin_id
            )
        );

        return $this->Curl->send($curl);
    }

}
