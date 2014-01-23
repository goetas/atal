<?php
namespace Goetas\ATal;

use DOMException;
use DOMText;
use DOMCdataSection;
use DOMNode;
use DOMProcessingInstruction;
use goetas\xml;
use Goetas\ATal\Extension\CoreExtension;
use Goetas\ATal\Extension\HTML5Extension;

class ATal implements \Twig_LoaderInterface
{

    const NS = 'ATal';

    /**
     *
     * @var array
     */
    protected $attributes = array();

    /**
     *
     * @var array
     */
    protected $extensions = array();

    /**
     *
     * @var array
     */
    protected $node = array();

    /**
     *
     * @var array
     */
    protected $preFlter = array();

    /**
     *
     * @var array
     */
    protected $domLoaders = array();

    /**
     *
     * @var array
     */
    protected $postFilter = array();

    protected $loader;

    protected $domLoader;

    function __construct(\Twig_LoaderInterface $loader, $domLoader = 'xml')
    {
        $this->loader = $loader;
        $this->domLoader = $domLoader;
        
        $this->extensions[] = new CoreExtension();
        $this->extensions[] = new HTML5Extension();
        
        foreach ($this->extensions as $extensions) {
            $this->attributes = array_merge($this->attributes, $extensions->getAttributes());
            $this->node = array_merge($this->node, $extensions->getNodes());
            $this->preFlter = array_merge($this->preFlter, $extensions->getPreFilters());
            $this->postFilter = array_merge($this->postFilter, $extensions->getPostFilters());
            $this->domLoaders = array_merge($this->domLoaders, $extensions->getDomLoaders());
        }
    }

    public function getCacheKey($name)
    {
        return $this->loader->getCacheKey($name);
    }

    public function isFresh($name, $time)
    {
        return $this->loader->isFresh($name, $time);
    }

    protected function loadDOM($string)
    {
        return $this->domLoaders[$this->loader]->createDOM($string);
    }

    /**
     * Ritorna una stringa del DOM presente in $xml
     *
     * @param
     *            $xml
     */
    public function getSource($name)
    {
        $cnt = $this->loader->getSource($name);
        
        foreach ($this->preFlter as $filter) {
            $cnt = call_user_func($filter, $cnt);
        }
        
        $domLoader = $this->getDomLoader();
        
        $xml = $domLoader->createDOM($cnt);
        
        $metadata = $domLoader->collectMetadata($xml, $cnt);
        
        $this->applyTemplatesToChilds($xml);
        
        $cnt = $domLoader->dumpDOM($xml, $metadata);
        
        foreach ($this->postFilter as $filter) {
            $cnt = call_user_func($filter, $cnt);
        }
        
        return $cnt;
    }

    protected function getDomLoader()
    {
        if (! isset($this->domLoaders[$this->domLoader])) {
            throw new Exception("Can't find a domloader called {$this->domLoader}");
        }
        return $this->domLoaders[$this->domLoader];
    }

    public function applyTemplates(\DOMElement $node)
    {
        if (isset($this->node[$node->namespaceURI][$node->localName])) {
            $this->node[$node->namespaceURI][$node->localName]->visit($node, $this);
        } elseif (isset($this->node[$node->namespaceURI]['__base__'])) {
            $this->node[$node->namespaceURI]['__base__']->visit($node, $this);
        } else {
            if ($node->namespaceURI === self::NS) {
                throw new Exception("Nodo sconosciuto {$node->namespaceURI}#{$node->localName}");
            }
            if ($this->applyTemplatesToAttributes($node)) {
                $this->applyTemplatesToChilds($node);
            }
        }
    }

    public function applyTemplatesToAttributes(\DOMNode $node)
    {
        $continueNode = true;
        foreach (iterator_to_array($node->attributes) as $attr) {
            if (isset($this->attributes[$attr->namespaceURI][$attr->localName])) {
                $attPlugin = $this->attributes[$attr->namespaceURI][$attr->localName];
            } elseif (isset($this->attributes[$attr->namespaceURI]['__base__'])) {
                $attPlugin = $this->attributes[$attr->namespaceURI]['__base__'];
            } else {
                continue;
            }
            
            $return = $attPlugin->visit($attr, $this);
            if ($return !== null) {
                $continueNode = $continueNode && ($return & Attribute::STOP_NODE);
                if ($return & Attribute::STOP_ATTRIBUTE) {
                    break;
                }
            }
        }
        return $continueNode;
    }

    public function applyTemplatesToChilds(\DOMNode $node)
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof \DOMElement) {
                $this->applyTemplates($child);
            }
        }
    }
}