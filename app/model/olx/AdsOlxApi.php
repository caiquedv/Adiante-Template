<?php

use Adianti\Database\TRecord;

class AdsOlxApi extends TRecord
{
    const TABLENAME = 'ads';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'max';

    private $user;
    private $stateObj;
    private $categoryObj;

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('status');
        parent::addAttribute('user_id');
        parent::addAttribute('state');
        parent::addAttribute('title');
        parent::addAttribute('category');
        parent::addAttribute('price');
        parent::addAttribute('price_negotiable');
        parent::addAttribute('description');
        parent::addAttribute('img');
        parent::addAttribute('views');
    }

    public function set_categoryObj(CategoriesOlxApi $object)
    {
        $this->categoryObj = $object;
        $this->category = $object->id;
    }

    public function get_categoryObj()
    {
        if (empty($this->categoryObj))
            $this->categoryObj = new CategoriesOlxApi($this->category);
    
        return $this->categoryObj;
    }

    public function set_stateObj(StatesOlxApi $object)
    {
        $this->stateObj = $object;
        $this->state = $object->id;
    }

    public function get_stateObj()
    {
        if (empty($this->stateObj)) {
            $this->stateObj = new StatesOlxApi($this->state);
        }

        return $this->stateObj;
    }

    public function set_user(UserOlxApi $object)
    {
        $this->user = $object;
        $this->user_id = $object->id;
    }

    public function get_user()
    {
        if (empty($this->user))
            $this->user = new UserOlxApi($this->user_id);
    
        return $this->user;
    }

    static public function getAds($user_id)
    {
        $ads = AdsOlxApi::where('user_id', '=', $user_id)->load();
        $response = [];

        if ($ads) {
            foreach ($ads as $value => $ad) {
                $response[] = $ad->toArray();
            }

            return $response;
        }
    }
}
