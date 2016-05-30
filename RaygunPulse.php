<?php

class RaygunPulse extends PluginBase {
    
    static protected $name = 'RaygunPulse';
    static protected $description = 'Implement RayGun Pulse to log your client side activities.';
    
    protected $storage = 'DbStorage';
    protected $settings = array(
        'apiKey'=>array(
            'type'=>'string',
            'label' => 'The api key to use for raygun pulse'
        )
    );

    public function __construct(PluginManager $manager, $id) {
        parent::__construct($manager, $id);

        $this->subscribe('beforeSurveyPage');
    }

    public function beforeSurveyPage()
    {
        // Get the js directory
	$jsPath=Yii::app()->assetManager->publish(dirname(__FILE__) . '/js/');
	// Register the js file
        Yii::app()->clientScript->registerScriptFile($jsPath.'/raygunPulse.register.js');
        Yii::app()->clientScript->registerScriptFile($jsPath.'/raygunPulse.js');
        // Get the settings
        $apiKey = $this->get('apiKey');
        $sessionId = Yii::app()->session->getSessionID();
        $aOption['apiKey'] = $apiKey;
        $aOption['identifier'] = $sessionId;
	// Create the javascript code to inject in the page
        $raygunPulseScript="raygunPulse.init(".ls_json_encode($aOption).");";
	// Inject js into the page
        Yii::app()->clientScript->registerScript("raygunPulse", $raygunPulseScript, CClientScript::POS_END);
    }
}