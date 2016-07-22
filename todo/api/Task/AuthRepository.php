<?php

namespace Task;
use Rtm\Rtm;
use RtmConfig\RtmConfig;
use lib\JsonArray;
// use Task\InvalidTokenException;

class AuthRepository {
  private $rtm;
  private $app;
  public function __construct($app) {
    $rtmConfig = new RtmConfig();
    $rtm = new Rtm;
    $rtm->setApiKey($rtmConfig->getApiKey());
    $rtm->setSecret($rtmConfig->getSecret());

    $this->rtm = $rtm;
    $this->app = $app;
  }

  private function getAuthService() {
    return $this->rtm->getService(Rtm::SERVICE_AUTH);
  }

  public function getToken() {
    $token = $this->app['session']->get('token');
    if($token == null && $this->app['session']->get('frob') != null) {
      $this->setupToken();
      $token = $this->app['session']->get('token');
    }
    if($token == null) {
      throw new InvalidTokenException();
    }
    return $token;
  }

  public function setupToken() {
    $frob = $this->app['session']->get('frob');
    $result = $this->getAuthService()->getToken($frob)->toArray();
    $token = $result['token'];
    $this->app['session']->set('token', $token);
    $this->app['session']->set('user', $result['user']);
    $this->app['session']->set('frob', null);// clear
    return $result;
  }

  public function createAuthUrl() {
    $frob = $this->getAuthService()->getFrob();
    $this->app['session']->set('frob', $frob);
    return $this->rtm->getAuthUrl($frob);
  }

  // override fun getAuthUrl(frob: Frob): String {
  //       val rtmParams = HashMap<RtmParam, RtmParamValueObject>()
  //       rtmParams.put(RtmParam.api_key, API_KEY)
  //       rtmParams.put(RtmParam.perms, RtmPerms.delete);
  //       rtmParams.put(RtmParam.frob, frob);
  //       rtmParams.put(RtmParam.v, V2());
  //       rtmParams.put(RtmParam.api_sig, ApiSig(SHARED_SECRET, rtmParams))
  //
  //       return rtmRequestUtil.authBaseUrl + "?" + HttpRequestUtil.createQuery(rtmParams, { it.value }, { it.rtmParamValue })
  //   }

}
