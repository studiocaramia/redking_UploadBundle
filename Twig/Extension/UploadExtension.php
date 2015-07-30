<?php

namespace Redking\Bundle\UploadBundle\Twig\Extension;

use Vich\UploaderBundle\Twig\Extension\UploaderExtension;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Redking\Bundle\UploadBundle\Naming\ResizedNamer;

class UploadExtension extends UploaderExtension
{

    /**
     * @var UploaderHelper $helper
     */
    private $helper;

    /**
     * Constructs a new instance of UploaderExtension.
     *
     * @param UploaderHelper $helper
     */
    public function __construct(UploaderHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     */
    public function getName()
    {
        return 'redking_upload';
    }

    /**
     * Returns a list of twig functions.
     *
     * @return array An array
     */
    public function getFunctions()
    {
        $names = array(
            'redking_upload_asset' => 'asset'
        );

        $funcs = array();
        foreach ($names as $twig => $local) {
            $funcs[$twig] = new \Twig_Function_Method($this, $local);
        }

        return $funcs;
    }

    /**
     * Gets the public path for the file associated with the uploadable
     * object.
     *
     * @param object $obj         The object.
     * @param string $mappingName The mapping name.
     * @param string $className   The object's class. Mandatory if $obj can't be used to determine it.
     *
     * @return string The public path.
     */
    public function asset($obj, $mappingName, $resize_name = null, $className = null)
    {
        $url = $this->helper->asset($obj, $mappingName, $className);
        
        if (is_null($resize_name)) {
            return $url;
        } elseif (!is_null($url)) {
            return ResizedNamer::getUrl($url, $resize_name);
        }
        return '';
    }
}
