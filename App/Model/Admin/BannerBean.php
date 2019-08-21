<?php

namespace App\Model\Admin;

/**
 * Class BannerBean
 * Create With Automatic Generator
 * @property int bannerId |
 * @property string bannerImg | banner图片
 * @property string bannerUrl | 跳转地址
 * @property int state | 状态0隐藏 1正常
 */
class BannerBean extends \EasySwoole\Spl\SplBean
{
    protected $bannerId;
    protected $bannerImg;
    protected $bannerUrl;
    protected $bannerName;
    protected $bannerDescription;
    protected $state;


    public function setBannerId($bannerId)
    {
        $this->bannerId = $bannerId;
    }


    public function getBannerId()
    {
        return $this->bannerId;
    }


    public function setBannerImg($bannerImg)
    {
        $this->bannerImg = $bannerImg;
    }


    public function getBannerImg()
    {
        return $this->bannerImg;
    }


    public function setBannerUrl($bannerUrl)
    {
        $this->bannerUrl = $bannerUrl;
    }


    public function getBannerUrl()
    {
        return $this->bannerUrl;
    }


    public function setState($state)
    {
        $this->state = $state;
    }


    public function getState()
    {
        return $this->state;
    }

    /**
     * @return mixed
     */
    public function getBannerName()
    {
        return $this->bannerName;
    }

    /**
     * @param mixed $bannerName
     */
    public function setBannerName($bannerName): void
    {
        $this->bannerName = $bannerName;
    }

    /**
     * @return mixed
     */
    public function getBannerDescription()
    {
        return $this->bannerDescription;
    }

    /**
     * @param mixed $bannerDescription
     */
    public function setBannerDescription($bannerDescription): void
    {
        $this->bannerDescription = $bannerDescription;
    }
}