<?php

/**
 * PluginDemo class
 *
 * Copyright (c) 2010 Glenn De Backer < glenn at simplicity dot be >
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * If you have any questions or comments, please email:
 * Glenn at simplicity dot be
 *
 * @author Glenn De Backer (glenn at  simplicity  dot be)
 *
 * @version 2.0
 */

namespace Simplicitylab\ShortCodePluginDemo;

class PluginDemo
{
    private $pluginFolderPath;
    private $availableFilesInPluginFolder;
    private $reflectiveClass;


    public function __construct()
    {
        $this->availablePlugins = array();

        $this->pluginFolderPath = "src" . DIRECTORY_SEPARATOR . "ShortCodePluginDemo" . DIRECTORY_SEPARATOR . "Plugins";

        $this->availableFilesInPluginFolder = $this->getFilesInPluginFolder();
    }


    private function getFilesInPluginFolder()
    {
        $files = array();

        if ($handle = opendir($this->pluginFolderPath)) {
            while (false !== ($filename = readdir($handle))) {
                if ($filename != "." && $filename != "..") {
                    $path_parts = pathinfo($filename);

                    if ($path_parts['extension'] == "php") {
                        // store filename
                        array_push($files, $path_parts['filename']);
                    }
                }
            }
            closedir($handle);
        }

        return $files;
    }

    private function isValidPlugin($pluginName, $pluginMethod, $parameters = array())
    {
        if (in_array($pluginName, $this->availableFilesInPluginFolder)) {
            // create classname including full namespace
            $className = "Simplicitylab\\ShortCodePluginDemo\\Plugins\\" . $pluginName;

            $this->pluginObject = new $className();
            $this->reflectiveClass = new \ReflectionClass($this->pluginObject);

            // be sure that class implented the iPlugin interface
            if ($this->reflectiveClass->isSubclassOf("Simplicitylab\ShortCodePluginDemo\Interfaces\PluginInterface")) {
                // be sure that method is implemented
                if ($this->reflectiveClass->hasMethod($pluginMethod)) {
                    // be sure that the method is public
                    $methodInstance = $this->reflectiveClass->getMethod($pluginMethod);
                    if ($methodInstance->isPublic()) {
                        // if there are parameters we need to execute
                        // some extra checks
                        if (count($parameters) > 0) {
                            $methodParameters = array();

                            // get method parameters and process them
                            // to a workable form
                            $reflectionParameters =  $methodInstance->getParameters();

                            foreach ($reflectionParameters as $reflectionParameter) {
                                array_push($methodParameters, $reflectionParameter->name);
                            }

                            if (count($methodParameters) > 0) {
                                $parameterNotFound = false;

                                foreach ($parameters as $parameter) {
                                    // if given parameter is not found in method parameters
                                    // set a flag indicating that the parameter couldn't be found
                                    if (!in_array($parameter['name'], $methodParameters)) {
                                        $parameterNotFound = true;
                                    }
                                }

                                if ($parameterNotFound) {
                                    return false;
                                } else {
                                    return true;
                                }
                            } else {
                                return false;
                            }
                        } else {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    public function process($parsed)
    {
        $returnValues = array();

        foreach ($parsed as $command) {
            if (array_key_exists('parent', $command['name']) && array_key_exists('child', $command['name'])) {
                // retrieve plugin name (Class name) and method name
                $pluginName = $command['name']['parent'];
                $methodName = $command['name']['child'];

                // if it is valid plugin
                if ($this->isValidPlugin($pluginName, $methodName, $command['parameters'])) {
                  // process any parameters
                    $parameterValues = array();
                    if (count($command['parameters']) > 0) {
                        // we only need to pass the parameter values
                        foreach ($command['parameters'] as $parameter) {
                            array_push($parameterValues, $parameter['value']);
                        }
                    }

                    // create classname including full namespace
                    $className = "Simplicitylab\\ShortCodePluginDemo\\Plugins\\" . $pluginName;

                    // Reflect method
                    $reflectionMethod = new \ReflectionMethod($className, $methodName);

                    // the way we invoke the method depends if there need to be paramaters passed or note
                    if (count($parameterValues) == 0) {
                        array_push($returnValues, $reflectionMethod->invoke($this->pluginObject));

                    } else {
                        array_push($returnValues, $reflectionMethod->invokeArgs($this->pluginObject, $parameterValues));
                    }
                } else {
                    return false;
                }
            }
        }

        return $returnValues;
    }

    public function getPluginObject()
    {
        return $this->pluginObject;
    }

    public function setPluginObject($activePluginObject)
    {
        $this->pluginObject = $activePluginObject;
        return $this;
    }
}
