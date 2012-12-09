<?php

/**
 * High level object oriented filesystem abstraction.
 *
 * @package php-filesystem
 * @author  Tristan Lins <tristan.lins@bit3.de>
 * @link    http://bit3.de
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Bit3\Filesystem;

/**
 * Skeleton for Filesystem implementors.
 *
 * @package php-filesystem
 * @author  Oliver Hoff <oliver@hofff.com>
 */
abstract class AbstractFilesystem
    implements Filesystem
{
	/**
	 * @var string The name of the config class used by instances of this
	 * 		filesystem implementation. Override in concrete classes to specify
	 * 		another config class.
	 */
	const CONFIG_CLASS = 'FilesystemConfig';
	
	/* (non-PHPdoc)
	 * @see Bit3\Filesystem.Filesystem::create()
	*/
	public static function create(FilesystemConfig $config) {
		// the instanceof operator has lexer issues...
		if(is_subclass_of($config, static::CONFIG_CLASS)) {
			$args = func_get_args();
			$clazz = new ReflectionClass(get_called_class());
			return $clazz->newInstanceArgs($args);
		}
		throw new Exception(); // TODO
	}
	
    /**
     * @var FilesystemConfig
     */
    protected $config;

    /**
     * @param FilesystemConfig $config
     */
    protected function __construct(FilesystemConfig $config, PublicURLProvider $provider)
    {
    	$this->config = clone $config;
    	$this->provider = $provider;
    	$this->prepareConfig();
    	$this->config->immutable();
    }

    /* (non-PHPdoc)
     * @see Bit3\Filesystem.Filesystem::getConfig()
     */
    public function getConfig()
    {
        return $this->config;
    }
    
    /**
     * Gets called before at construction time before the config is made
     * immutable. Override in concrete classes to extend or alter behavior.
     */
    protected function prepareConfig() {
    	$this->config->setBasePath(Util::normalizePath('/' . $this->config->getBasePath()) . '/');
    }
	
	protected $provider;
	
	/* (non-PHPdoc)
	 * @see Bit3\Filesystem.FilesystemConfig::getPublicURLProvider()
	 */
	public function getPublicURLProvider() {
		return $this->provider;
	}
	
	/* (non-PHPdoc)
	 * @see Bit3\Filesystem.FilesystemConfig::setPublicURLProvider()
	 */
	public function setPublicURLProvider(PublicURLProvider $provider = null) {
		$this->provider = $provider;
	}
}
