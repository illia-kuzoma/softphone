<?php
namespace App\Zoho\V1;

class Organization extends ZohoV1
{
    const WELNESSLIVING = 'wellnessliving';

    public function getAll()
    {
        $organizations = $this->request(
            "https://desk.zoho.com/api/v1/organizations"
        );
        if(empty($organizations))
        {
            throw new \Exception(sprintf("Organizations list is empty!"));
        }
        $this->checkResponse($organizations);
        return $organizations;
    }

    private function getOrgId($s_org_name)
    {
        $a_org = $this->getAll()['data'];
        $i_wellnessliving_key = -1;
        foreach ($a_org as $key => $org) {
            foreach ($org as $data) {
                if(mb_strtolower($data) === $s_org_name){
                    $i_wellnessliving_key = $key;
                    break 2;
                }
            }
        }
        if($i_wellnessliving_key >= 0)
        {
            return $a_org[$i_wellnessliving_key]['id'];
        }
        return null;
    }

    public function getIdWellnessliving()
    {
        $org_id = $this->getOrgId(self::WELNESSLIVING);
        if(!$org_id)
        {
            throw new \Exception(sprintf("Org Id >>> %s <<< isn't correct!",$org_id));
        }
        return $org_id;
    }
}
