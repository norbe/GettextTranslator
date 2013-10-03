<?php

namespace GettextTranslator\DI;

use Nette;

class Extension extends Nette\DI\CompilerExtension
{
	/** @var array */
	private $defaults = array(
		'lang' => 'en',
		'files' => array(),
		'layout' => 'horizontal',
		'height' => 450
	);


	public function loadConfiguration()
	{
		$config = $this->getConfig($this->defaults);
		$builder = $this->getContainerBuilder();

		$translator = $builder->addDefinition($this->prefix('translator'));
		$translator->setClass('GettextTranslator\Gettext', array('@session', '@cacheStorage', '@httpResponse'));
		$translator->addSetup('setLang', array($config['lang']));
	        $translator->addSetup('setProductionMode', array($builder->expand('%productionMode%')));
	
	        // at least one language file must be defined
	        if (count($config['files']) === 0) {
	            throw new InvalidConfigException('Language file(s) must be defined.');
	        }
	        foreach ($config['files'] as $id => $file) {
	            $translator->addSetup('addFile', array($file, $id));
	        }

		$translator->addSetup('GettextTranslator\Panel::register', array('@application', '@self', '@session', '@httpRequest', $config['layout'], $config['height']));
	}

}

class InvalidConfigException extends Nette\InvalidStateException {

}
