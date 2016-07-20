<?php

namespace Task;
use Rtm\Rtm;
use RtmConfig\RtmConfig;
use lib\JsonArray;
class AuthRepository {
  private $rtm;
  public function __construct() {
    $rtmConfig = new RtmConfig();
    $rtm = new Rtm;
    $rtm->setApiKey($rtmConfig->getApiKey());
    $rtm->setSecret($rtmConfig->getSecret());

    $this->rtm = $rtm;
  }

  private function getAuthService() {
    return $this->rtm->getService(Rtm::SERVICE_AUTH);
  }

  public function getFrob() {
    $result = $this->getAuthService()->getFrob();
    return $result;
  }

  public function getToken($frob) {
    $result = $this->getAuthService()->getToken($frob);
    return $result;
  }

  public function createAuthUrl($frob) {
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
