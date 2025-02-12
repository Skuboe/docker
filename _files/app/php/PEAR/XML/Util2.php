<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * XML_Util2
 *
 * XML Utilities package
 *
 * PHP version 5
 *
 * LICENSE:
 *
 * Copyright (c) 2003-2008 Stephan Schmidt <schst@php.net>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *    * Redistributions of source code must retain the above copyright
 *      notice, this list of conditions and the following disclaimer.
 *    * Redistributions in binary form must reproduce the above copyright
 *      notice, this list of conditions and the following disclaimer in the
 *      documentation and/or other materials provided with the distribution.
 *    * The name of the author may not be used to endorse or promote products
 *      derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
 * IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  XML
 * @package   XML_Util2
 * @author    Stephan Schmidt <schst@php.net>
 * @copyright 2003-2008 Stephan Schmidt <schst@php.net>
 * @license   http://opensource.org/licenses/bsd-license New BSD License
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/XML_Util2
 */

require_once 'XML/Util2/Exception.php';

/**
 * utility class for working with XML documents
 *

 * @category  XML
 * @package   XML_Util2
 * @author    Stephan Schmidt <schst@php.net>
 * @copyright 2003-2008 Stephan Schmidt <schst@php.net>
 * @license   http://opensource.org/licenses/bsd-license New BSD License
 * @version   Release: 0.2.0
 * @link      http://pear.php.net/package/XML_Util2
 */
class XML_Util2
{
    /**
     * error code for invalid chars in XML name
     */
    const ERROR_INVALID_CHARS = 51;

    /**
     * error code for invalid chars in XML name
     */
    const ERROR_INVALID_START = 52;

    /**
     * error code for non-scalar tag content
     */
    const ERROR_NON_SCALAR_CONTENT = 60;

    /**
     * error code for missing tag name
     */
    const ERROR_NO_TAG_NAME = 61;

    /**
     * replace XML entities
     */
    const REPLACE_ENTITIES = 1;

    /**
     * embedd content in a CData Section
     */
    const CDATA_SECTION = 5;

    /**
     * do not replace entitites
     */
    const ENTITIES_NONE = 0;

    /**
     * replace all XML entitites
     * This setting will replace <, >, ", ' and &
     */
    const ENTITIES_XML = 1;

    /**
     * replace only required XML entitites
     * This setting will replace <, " and &
     */
    const ENTITIES_XML_REQUIRED = 2;

    /**
     * replace HTML entitites
     * @link http://www.php.net/htmlentities
     */
    const ENTITIES_HTML = 3;

    /**
     * Collapse all empty tags.
     */
    const COLLAPSE_ALL = 1;

    /**
     * Collapse only empty XHTML tags that have no end tag.
     */
    const COLLAPSE_XHTML_ONLY = 2;

    /**
     * return API version
     *
     * @return string $version API version
     * @access public
     */
    public function apiVersion()
    {
        return '2.0';
    }

    /**
     * replace XML entities
     *
     * With the optional second parameter, you may select, which
     * entities should be replaced.
     *
     * <code>
     * require_once 'XML/Util2.php';
     *
     * $util = new XML_Util2();
     *
     * // replace XML entites:
     * $string = $util->replaceEntities('This string contains < & >.');
     * </code>
     *
     * With the optional third parameter, you may pass the character encoding
     * <code>
     * require_once 'XML/Util2.php';
     *
     * $util = new XML_Util2();
     *
     * // replace XML entites in UTF-8:
     * $string = $util->replaceEntities(
     *     'This string contains < & > as well as ä, ö, ß, à and ê',
     *     XML_Util2::ENTITIES_HTML,
     *     'UTF-8'
     * );
     * </code>
     *
     * @param string $string          string where XML special chars
     *                                should be replaced
     * @param int    $replaceEntities setting for entities in attribute values
     *                                (one of XML_Util2::ENTITIES_XML,
     *                                XML_Util2::ENTITIES_XML_REQUIRED,
     *                                XML_Util2::ENTITIES_HTML)
     * @param string $encoding        encoding value (if any)...
     *                                must be a valid encoding as determined
     *                                by the htmlentities() function
     *
     * @return string string with replaced chars
     * @access public
     * @see reverseEntities()
     */
    public function replaceEntities($string, $replaceEntities = XML_Util2::ENTITIES_XML,
        $encoding = 'ISO-8859-1')
    {
        switch ($replaceEntities) {
        case XML_Util2::ENTITIES_XML:
            return strtr($string, array(
                '&'  => '&amp;',
                '>'  => '&gt;',
                '<'  => '&lt;',
                '"'  => '&quot;',
                '\'' => '&apos;' ));
            break;
        case XML_Util2::ENTITIES_XML_REQUIRED:
            return strtr($string, array(
                '&' => '&amp;',
                '<' => '&lt;',
                '"' => '&quot;' ));
            break;
        case XML_Util2::ENTITIES_HTML:
            return htmlentities($string, ENT_COMPAT, $encoding);
            break;
        }
        return $string;
    }

    /**
     * reverse XML entities
     *
     * With the optional second parameter, you may select, which
     * entities should be reversed.
     *
     * <code>
     * require_once 'XML/Util2.php';
     *
     * $util = new XML_Util2();
     *
     * // reverse XML entites:
     * $string = $util->reverseEntities('This string contains &lt; &amp; &gt;.');
     * </code>
     *
     * With the optional third parameter, you may pass the character encoding
     * <code>
     * require_once 'XML/Util2.php';
     *
     * $util = new XML_Util2();
     *
     * // reverse XML entites in UTF-8:
     * $string = $util->reverseEntities(
     *     'This string contains &lt; &amp; &gt; as well as'
     *     . ' &auml;, &ouml;, &szlig;, &agrave; and &ecirc;',
     *     XML_Util2::ENTITIES_HTML,
     *     'UTF-8'
     * );
     * </code>
     *
     * @param string $string          string where XML special chars
     *                                should be replaced
     * @param int    $replaceEntities setting for entities in attribute values
     *                                (one of XML_Util2::ENTITIES_XML,
     *                                XML_Util2::ENTITIES_XML_REQUIRED,
     *                                XML_Util2::ENTITIES_HTML)
     * @param string $encoding        encoding value (if any)...
     *                                must be a valid encoding as determined
     *                                by the html_entity_decode() function
     *
     * @return string string with replaced chars
     * @access public
     * @see replaceEntities()
     */
    public function reverseEntities($string, $replaceEntities = XML_Util2::ENTITIES_XML,
        $encoding = 'ISO-8859-1')
    {
        switch ($replaceEntities) {
        case XML_Util2::ENTITIES_XML:
            return strtr($string, array(
                '&amp;'  => '&',
                '&gt;'   => '>',
                '&lt;'   => '<',
                '&quot;' => '"',
                '&apos;' => '\'' ));
            break;
        case XML_Util2::ENTITIES_XML_REQUIRED:
            return strtr($string, array(
                '&amp;'  => '&',
                '&lt;'   => '<',
                '&quot;' => '"' ));
            break;
        case XML_Util2::ENTITIES_HTML:
            return html_entity_decode($string, ENT_COMPAT, $encoding);
            break;
        }
        return $string;
    }

    /**
     * build an xml declaration
     *
     * <code>
     * require_once 'XML/Util2.php';
     *
     * $util = new XML_Util2();
     *
     * // get an XML declaration:
     * $xmlDecl = $util->getXMLDeclaration('1.0', 'UTF-8', true);
     * </code>
     *
     * @param string $version    xml version
     * @param string $encoding   character encoding
     * @param bool   $standalone document is standalone (or not)
     *
     * @return string xml declaration
     * @access public
     * @uses attributesToString() to serialize the attributes of the XML declaration
     */
    public function getXMLDeclaration($version = '1.0', $encoding = null,
        $standalone = null)
    {
        $attributes = array(
            'version' => $version,
        );
        // add encoding
        if ($encoding !== null) {
            $attributes['encoding'] = $encoding;
        }
        // add standalone, if specified
        if ($standalone !== null) {
            $attributes['standalone'] = $standalone ? 'yes' : 'no';
        }

        return sprintf('<?xml%s?>',
            $this->attributesToString($attributes, false));
    }

    /**
     * build a document type declaration
     *
     * <code>
     * require_once 'XML/Util2.php';
     *
     * $util = new XML_Util2();
     *
     * // get a doctype declaration:
     * $util = new XML_Util2();
     * $xmlDecl = $util->getDocTypeDeclaration('rootTag','myDocType.dtd');
     * </code>
     *
     * @param string $root        name of the root tag
     * @param string $uri         uri of the doctype definition
     *                            (or array with uri and public id)
     * @param string $internalDtd internal dtd entries
     *
     * @return string doctype declaration
     * @access public
     * @since 0.2
     */
    public function getDocTypeDeclaration($root, $uri = null, $internalDtd = null)
    {
        if (is_array($uri)) {
            $ref = sprintf(' PUBLIC "%s" "%s"', $uri['id'], $uri['uri']);
        } elseif (!empty($uri)) {
            $ref = sprintf(' SYSTEM "%s"', $uri);
        } else {
            $ref = '';
        }

        if (empty($internalDtd)) {
            return sprintf('<!DOCTYPE %s%s>', $root, $ref);
        } else {
            return sprintf("<!DOCTYPE %s%s [\n%s\n]>", $root, $ref, $internalDtd);
        }
    }

    /**
     * create string representation of an attribute list
     *
     * <code>
     * require_once 'XML/Util2.php';
     *
     * $util = new XML_Util2();
     *
     * // build an attribute string
     * $att = array(
     *              'foo'   =>  'bar',
     *              'argh'  =>  'tomato'
     *            );
     *
     * $util = new XML_Util2();
     * $attList = $util->attributesToString($att);
     * </code>
     *
     * @param array      $attributes attribute array
     * @param bool|array $sort       sort attribute list alphabetically,
     *                               may also be an assoc array containing
     *                               the keys 'sort', 'multiline', 'indent',
     *                               'linebreak' and 'entities'
     * @param bool       $multiline  use linebreaks, if more than
     *                               one attribute is given
     * @param string     $indent     string used for indentation of
     *                               multiline attributes
     * @param string     $linebreak  string used for linebreaks of
     *                               multiline attributes
     * @param int        $entities   setting for entities in attribute values
     *                               (one of XML_Util2::ENTITIES_NONE,
     *                               XML_Util2::ENTITIES_XML,
     *                               XML_Util2::ENTITIES_XML_REQUIRED,
     *                               XML_Util2::ENTITIES_HTML)
     *
     * @return string string representation of the attributes
     * @access public
     * @uses replaceEntities() to replace XML entities in attribute values
     * @todo allow sort also to be an options array
     */
    public function attributesToString($attributes, $sort = true, $multiline = false,
        $indent = '    ', $linebreak = "\n", $entities = XML_Util2::ENTITIES_XML)
    {
        /*
         * second parameter may be an array
         */
        if (is_array($sort)) {
            if (isset($sort['multiline'])) {
                $multiline = $sort['multiline'];
            }
            if (isset($sort['indent'])) {
                $indent = $sort['indent'];
            }
            if (isset($sort['linebreak'])) {
                $multiline = $sort['linebreak'];
            }
            if (isset($sort['entities'])) {
                $entities = $sort['entities'];
            }
            if (isset($sort['sort'])) {
                $sort = $sort['sort'];
            } else {
                $sort = true;
            }
        }
        $string = '';
        if (is_array($attributes) && !empty($attributes)) {
            if ($sort) {
                ksort($attributes);
            }
            if ( !$multiline || count($attributes) == 1) {
                foreach ($attributes as $key => $value) {
                    if ($entities != XML_Util2::ENTITIES_NONE) {
                        if ($entities === XML_Util2::CDATA_SECTION) {
                            $entities = XML_Util2::ENTITIES_XML;
                        }
                        $value = $this->replaceEntities($value, $entities);
                    }
                    $string .= ' ' . $key . '="' . $value . '"';
                }
            } else {
                $first = true;
                foreach ($attributes as $key => $value) {
                    if ($entities != XML_Util2::ENTITIES_NONE) {
                        $value = $this->replaceEntities($value, $entities);
                    }
                    if ($first) {
                        $string .= ' ' . $key . '="' . $value . '"';
                        $first   = false;
                    } else {
                        $string .= $linebreak . $indent . $key . '="' . $value . '"';
                    }
                }
            }
        }
        return $string;
    }

    /**
     * Collapses empty tags.
     *
     * @param string $xml  XML
     * @param int    $mode Whether to collapse all empty tags (XML_Util2::COLLAPSE_ALL)
     *                      or only XHTML (XML_Util2::COLLAPSE_XHTML_ONLY) ones.
     *
     * @return string XML
     * @access public
     * @todo PEAR CS - unable to avoid "space after open parens" error
     *       in the IF branch
     */
    public function collapseEmptyTags($xml, $mode = XML_Util2::COLLAPSE_ALL)
    {
        if ($mode == XML_Util2::COLLAPSE_XHTML_ONLY) {
            return preg_replace(
                '/<(area|base(?:font)?|br|col|frame|hr|img|input|isindex|link|meta|'
                . 'param)([^>]*)><\/\\1>/s',
                '<\\1\\2 />',
                $xml);
        } else {
            return preg_replace('/<(\w+)([^>]*)><\/\\1>/s', '<\\1\\2 />', $xml);
        }
    }

    /**
     * create a tag
     *
     * This method will call XML_Util2::createTagFromArray(), which
     * is more flexible.
     *
     * <code>
     * require_once 'XML/Util2.php';
     *
     * $util = new XML_Util2();
     *
     * // create an XML tag:
     * $tag = $util->createTag('myNs:myTag',
     *     array('foo' => 'bar'),
     *     'This is inside the tag',
     *     'http://www.w3c.org/myNs#');
     * </code>
     *
     * @param string $qname           qualified tagname (including namespace)
     * @param array  $attributes      array containg attributes
     * @param mixed  $content         the content
     * @param string $namespaceUri    URI of the namespace
     * @param int    $replaceEntities whether to replace XML special chars in
     *                                content, embedd it in a CData section
     *                                or none of both
     * @param bool   $multiline       whether to create a multiline tag where
     *                                each attribute gets written to a single line
     * @param string $indent          string used to indent attributes
     *                                (_auto indents attributes so they start
     *                                at the same column)
     * @param string $linebreak       string used for linebreaks
     * @param bool   $sortAttributes  Whether to sort the attributes or not
     *
     * @return string XML tag
     * @access public
     * @see createTagFromArray()
     * @uses createTagFromArray() to create the tag
     */
    public function createTag($qname, $attributes = array(), $content = null,
        $namespaceUri = null, $replaceEntities = XML_Util2::REPLACE_ENTITIES,
        $multiline = false, $indent = '_auto', $linebreak = "\n",
        $sortAttributes = true)
    {
        $tag = array(
            'qname'      => $qname,
            'attributes' => $attributes
        );

        // add tag content
        if ($content !== null) {
            $tag['content'] = $content;
        }

        // add namespace Uri
        if ($namespaceUri !== null) {
            $tag['namespaceUri'] = $namespaceUri;
        }

        return $this->createTagFromArray($tag, $replaceEntities, $multiline,
            $indent, $linebreak, $sortAttributes);
    }

    /**
     * create a tag from an array
     * this method awaits an array in the following format
     * <pre>
     * array(
     *     // qualified name of the tag
     *     'qname' => $qname
     *
     *     // namespace prefix (optional, if qname is specified or no namespace)
     *     'namespace' => $namespace
     *
     *     // local part of the tagname (optional, if qname is specified)
     *     'localpart' => $localpart,
     *
     *     // array containing all attributes (optional)
     *     'attributes' => array(),
     *
     *     // tag content (optional)
     *     'content' => $content,
     *
     *     // namespaceUri for the given namespace (optional)
     *     'namespaceUri' => $namespaceUri
     * )
     * </pre>
     *
     * <code>
     * require_once 'XML/Util2.php';
     *
     * $util = new XML_Util2();
     *
     * $tag = array(
     *     'qname'        => 'foo:bar',
     *     'namespaceUri' => 'http://foo.com',
     *     'attributes'   => array('key' => 'value', 'argh' => 'fruit&vegetable'),
     *     'content'      => 'I\'m inside the tag',
     * );
     * // creating a tag with qualified name and namespaceUri
     * $string = $util->createTagFromArray($tag);
     * </code>
     *
     * @param array  $tag             tag definition
     * @param int    $replaceEntities whether to replace XML special chars in
     *                                content, embedd it in a CData section
     *                                or none of both
     * @param bool   $multiline       whether to create a multiline tag where each
     *                                attribute gets written to a single line
     * @param string $indent          string used to indent attributes
     *                                (_auto indents attributes so they start
     *                                at the same column)
     * @param string $linebreak       string used for linebreaks
     * @param bool   $sortAttributes  Whether to sort the attributes or not
     *
     * @return string XML tag
     * @access public
     * @see createTag()
     * @uses attributesToString() to serialize the attributes of the tag
     * @uses splitQualifiedName() to get local part and namespace of a qualified name
     * @uses createCDataSection()
     * @uses raiseError()
     */
    public function createTagFromArray($tag, $replaceEntities = XML_Util2::REPLACE_ENTITIES,
        $multiline = false, $indent = '_auto', $linebreak = "\n",
        $sortAttributes = true)
    {
        if (isset($tag['content']) && !is_scalar($tag['content'])) {
            return $this->raiseError('Supplied non-scalar value as tag content',
            XML_Util2::ERROR_NON_SCALAR_CONTENT);
        }

        if (!isset($tag['qname']) && !isset($tag['localPart'])) {
            return $this->raiseError('You must either supply a qualified name '
                . '(qname) or local tag name (localPart).',
                XML_Util2::ERROR_NO_TAG_NAME);
        }

        // if no attributes hav been set, use empty attributes
        if (!isset($tag['attributes']) || !is_array($tag['attributes'])) {
            $tag['attributes'] = array();
        }

        if (isset($tag['namespaces'])) {
            foreach ($tag['namespaces'] as $ns => $uri) {
                $tag['attributes']['xmlns:' . $ns] = $uri;
            }
        }

        if (!isset($tag['qname'])) {
            // qualified name is not given

            // check for namespace
            if (isset($tag['namespace']) && !empty($tag['namespace'])) {
                $tag['qname'] = $tag['namespace'] . ':' . $tag['localPart'];
            } else {
                $tag['qname'] = $tag['localPart'];
            }
        } elseif (isset($tag['namespaceUri']) && !isset($tag['namespace'])) {
            // namespace URI is set, but no namespace

            $parts = $this->splitQualifiedName($tag['qname']);

            $tag['localPart'] = $parts['localPart'];
            if (isset($parts['namespace'])) {
                $tag['namespace'] = $parts['namespace'];
            }
        }

        if (isset($tag['namespaceUri']) && !empty($tag['namespaceUri'])) {
            // is a namespace given
            if (isset($tag['namespace']) && !empty($tag['namespace'])) {
                $tag['attributes']['xmlns:' . $tag['namespace']] =
                    $tag['namespaceUri'];
            } else {
                // define this Uri as the default namespace
                $tag['attributes']['xmlns'] = $tag['namespaceUri'];
            }
        }

        // check for multiline attributes
        if ($multiline === true) {
            if ($indent === '_auto') {
                $indent = str_repeat(' ', (strlen($tag['qname'])+2));
            }
        }

        // create attribute list
        $attList = $this->attributesToString($tag['attributes'],
            $sortAttributes, $multiline, $indent, $linebreak);
        if (!isset($tag['content']) || (string)$tag['content'] == '') {
            $tag = sprintf('<%s%s />', $tag['qname'], $attList);
        } else {
            switch ($replaceEntities) {
            case XML_Util2::ENTITIES_NONE:
                break;
            case XML_Util2::CDATA_SECTION:
                $tag['content'] = $this->createCDataSection($tag['content']);
                break;
            default:
                $tag['content'] = $this->replaceEntities($tag['content'],
                    $replaceEntities);
                break;
            }
            $tag = sprintf('<%s%s>%s</%s>', $tag['qname'], $attList, $tag['content'],
                $tag['qname']);
        }
        return $tag;
    }

    /**
     * create a start element
     *
     * <code>
     * require_once 'XML/Util2.php';
     *
     * $util = new XML_Util2();
     *
     * // create an XML start element:
     * $tag = $util->createStartElement('myNs:myTag',
     *     array('foo' => 'bar') ,'http://www.w3c.org/myNs#');
     * </code>
     *
     * @param string $qname          qualified tagname (including namespace)
     * @param array  $attributes     array containg attributes
     * @param string $namespaceUri   URI of the namespace
     * @param bool   $multiline      whether to create a multiline tag where each
     *                               attribute gets written to a single line
     * @param string $indent         string used to indent attributes (_auto indents
     *                               attributes so they start at the same column)
     * @param string $linebreak      string used for linebreaks
     * @param bool   $sortAttributes Whether to sort the attributes or not
     *
     * @return string XML start element
     * @access public
     * @see createEndElement(), createTag()
     */
    public function createStartElement($qname, $attributes = array(), $namespaceUri = null,
        $multiline = false, $indent = '_auto', $linebreak = "\n",
        $sortAttributes = true)
    {
        // if no attributes hav been set, use empty attributes
        if (!isset($attributes) || !is_array($attributes)) {
            $attributes = array();
        }

        if ($namespaceUri != null) {
            $parts = $this->splitQualifiedName($qname);
        }

        // check for multiline attributes
        if ($multiline === true) {
            if ($indent === '_auto') {
                $indent = str_repeat(' ', (strlen($qname)+2));
            }
        }

        if ($namespaceUri != null) {
            // is a namespace given
            if (isset($parts['namespace']) && !empty($parts['namespace'])) {
                $attributes['xmlns:' . $parts['namespace']] = $namespaceUri;
            } else {
                // define this Uri as the default namespace
                $attributes['xmlns'] = $namespaceUri;
            }
        }

        // create attribute list
        $attList = $this->attributesToString($attributes, $sortAttributes,
            $multiline, $indent, $linebreak);
        $element = sprintf('<%s%s>', $qname, $attList);
        return  $element;
    }

    /**
     * create an end element
     *
     * <code>
     * require_once 'XML/Util2.php';
     *
     * $util = new XML_Util2();
     *
     * // create an XML start element:
     * $tag = $util->createEndElement('myNs:myTag');
     * </code>
     *
     * @param string $qname qualified tagname (including namespace)
     *
     * @return string XML end element
     * @access public
     * @see createStartElement(), createTag()
     */
    public function createEndElement($qname)
    {
        $element = sprintf('</%s>', $qname);
        return $element;
    }

    /**
     * create an XML comment
     *
     * <code>
     * require_once 'XML/Util2.php';
     *
     * $util = new XML_Util2();
     *
     * // create an XML start element:
     * $tag = $util->createComment('I am a comment');
     * </code>
     *
     * @param string $content content of the comment
     *
     * @return string XML comment
     * @access public
     */
    public function createComment($content)
    {
        $comment = sprintf('<!-- %s -->', $content);
        return $comment;
    }

    /**
     * create a CData section
     *
     * <code>
     * require_once 'XML/Util2.php';
     *
     * $util = new XML_Util2();
     *
     * // create a CData section
     * $tag = $util->createCDataSection('I am content.');
     * </code>
     *
     * @param string $data data of the CData section
     *
     * @return string CData section with content
     * @access public
     */
    function createCDataSection($data)
    {
        return sprintf('<![CDATA[%s]]>',
            preg_replace('/\]\]>/', ']]]]><![CDATA[>', strval($data)));

    }

    /**
     * split qualified name and return namespace and local part
     *
     * <code>
     * require_once 'XML/Util2.php';
     *
     * $util = new XML_Util2();
     *
     * // split qualified tag
     * $parts = $util->splitQualifiedName('xslt:stylesheet');
     * </code>
     * the returned array will contain two elements:
     * <pre>
     * array(
     *     'namespace' => 'xslt',
     *     'localPart' => 'stylesheet'
     * );
     * </pre>
     *
     * @param string $qname     qualified tag name
     * @param string $defaultNs default namespace (optional)
     *
     * @return array array containing namespace and local part
     * @access public
     */
    function splitQualifiedName($qname, $defaultNs = null)
    {
        if (strstr($qname, ':')) {
            $tmp = explode(':', $qname);
            return array(
                'namespace' => $tmp[0],
                'localPart' => $tmp[1]
            );
        }
        return array(
            'namespace' => $defaultNs,
            'localPart' => $qname
        );
    }

    /**
     * check, whether string is valid XML name
     *
     * <p>XML names are used for tagname, attribute names and various
     * other, lesser known entities.</p>
     * <p>An XML name may only consist of alphanumeric characters,
     * dashes, undescores and periods, and has to start with a letter
     * or an underscore.</p>
     *
     * <code>
     * require_once 'XML/Util2.php';
     *
     * $util = new XML_Util2();
     *
     * // verify tag name
     * $result = $util->isValidName('invalidTag?');
     * if (is_a($result, 'PEAR_Error')) {
     *    print 'Invalid XML name: ' . $result->getMessage();
     * }
     * </code>
     *
     * @param string $string string that should be checked
     *
     * @return mixed true, if string is a valid XML name, PEAR error otherwise
     * @access public
     * @todo support for other charsets
     * @todo PEAR CS - unable to avoid 85-char limit on second preg_match
     */
    function isValidName($string)
    {
        // check for invalid chars
        if (!preg_match('/^[[:alpha:]_]\\z/', $string{0})) {
            return $this->raiseError('XML names may only start with letter '
                . 'or underscore', XML_Util2::ERROR_INVALID_START);
        }

        // check for invalid chars
        if (!preg_match('/^([[:alpha:]_]([[:alnum:]\-\.]*)?:)?[[:alpha:]_]([[:alnum:]\_\-\.]+)?\\z/',
            $string)
        ) {
            return $this->raiseError('XML names may only contain alphanumeric '
                . 'chars, period, hyphen, colon and underscores',
                XML_Util2::ERROR_INVALID_CHARS);
        }
        // XML name is valid
        return true;
    }

    /**
     * 
     *
     * @param string $msg  error message
     * @param int    $code error code
     *
     * @return PEAR_Error
     * @access public
     * @todo PEAR CS - should this use include_once instead?
     */
    public function raiseError($msg, $code)
    {
        throw new XML_Util2_Exception($msg, $code);
    }
}
?>
