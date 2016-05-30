<?php

/**
 * Raygun Pulse Plugin for LimeSurvey
 * Implement RayGun Pulse to log your client side activities.
 *
 * @author Frederik Prijck <http://www.frederikprijck.net/>
 * @copyright 2016 Frederik Prijck <http://www.frederikprijck.net/>
 * @license MIT
 * @license https://opensource.org/licenses/MIT MIT License (MIT)
 * @version 1.0.0
 *
 */

class RaygunPulse extends \ls\pluginmanager\PluginBase {
    
    static protected $name = 'RaygunPulse';
    static protected $description = 'Implement RayGun Pulse to log your client side activities.';
    
    protected $storage = 'DbStorage';
    protected $settings = array(
        'apiKey'=>array(
            'type'=>'string',
            'label' => 'The api key to use for raygun pulse'
        )
    );

    public function init() {
        $this->subscribe('beforeSurveyPage');
    }
    
    /*
     * Hook to handle the 'beforeSurveyPage' event
     * See: https://manual.limesurvey.org/BeforeSurveyPage
    */
    public function beforeSurveyPage()
    {
        $apiKey = $this->get('apiKey');
        $isApiKeyProvided = trim($apiKey) !== '';
        
        if($isApiKeyProvided){
            $this->loadPlugin($apiKey);
        }       
    }
    
    /*
     * Method used to load the plugin
    */
    private function loadPlugin($apiKey){
      // Get the js directory
	  $jsPath=Yii::app()->assetManager->publish(dirname(__FILE__) . '/js/');
	  // Register the js file
      Yii::app()->clientScript->registerScriptFile($jsPath.'/raygunPulse.register.js');
      Yii::app()->clientScript->registerScriptFile($jsPath.'/raygunPulse.js');
       
      $aOption['apiKey'] = $apiKey;
      $aOption['identifier'] = $this->getUserIdentifier();
      // Create the javascript code to inject in the page
      $raygunPulseScript = "raygunPulse.init(".ls_json_encode($aOption).");";
      // Inject js into the page
      Yii::app()->clientScript->registerScript("raygunPulse", $raygunPulseScript, CClientScript::POS_END);
    }
    
    /*
     * Method used to generate the identifier for a user
    */
    private function getUserIdentifier(){
        $surveyId=Yii::app()->session['LEMsid'];
        $surveySession = Yii::app()->session['survey_' . $surveyId];
        $token =  $surveySession['token'];
        
        // Check if the survey uses tokens
        // Currenly only session Id is supported as identifier
        $canUseToken = trim($token) !== '';
        
        if($canUseToken == false){
            // When tokens are not used: use [sessionId] as identifier
            $sessionId = Yii::app()->session->getSessionID();
            return $sessionId;
        }else{
            // When tokens are used: use [surveyId]-[token] as identifier
            return $surveyId . '-' . $token;
        }
    }
    
}