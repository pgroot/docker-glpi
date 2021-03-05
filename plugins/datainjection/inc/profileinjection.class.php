<?php
/*
 * @version $Id: HEADER 14684 2011-06-11 06:32:40Z remi $
 LICENSE

 This file is part of the datainjection plugin.

 Datainjection plugin is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 Datainjection plugin is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with datainjection. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
 @package   datainjection
 @author    the datainjection plugin team
 @copyright Copyright (c) 2010-2017 Datainjection plugin team
 @license   GPLv2+
            http://www.gnu.org/licenses/gpl.txt
 @link      https://github.com/pluginsGLPI/datainjection
 @link      http://www.glpi-project.org/
 @since     2009
 ---------------------------------------------------------------------- */

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}

class PluginDatainjectionProfileInjection extends Profile
                                          implements PluginDatainjectionInjectionInterface
{


   static function getTable($classname = null) {

      $parenttype = get_parent_class();
      return $parenttype::getTable();
   }


   function isPrimaryType() {

      return true;
   }


   function connectedTo() {

      return [];
   }


    /**
    * @see plugins/datainjection/inc/PluginDatainjectionInjectionInterface::getOptions()
   **/
   function getOptions($primary_type = '') {

      return Search::getOptions(get_parent_class($this));
   }


    /**
    * @param $field_name
    * @param $data
    * @param $mandatory
   **/
   function checkType($field_name, $data, $mandatory) {

      switch ($field_name) {
         case 'right_rw' :
            return (in_array($data, ['r', 'w'])
                 ?PluginDatainjectionCommonInjectionLib::SUCCESS
                 :PluginDatainjectionCommonInjectionLib::TYPE_MISMATCH);

         case 'right_r' :
            return (($data=='r')?PluginDatainjectionCommonInjectionLib::SUCCESS
                             :PluginDatainjectionCommonInjectionLib::TYPE_MISMATCH);

         case 'right_w' :
            return (($data=='w')?PluginDatainjectionCommonInjectionLib::SUCCESS
                             :PluginDatainjectionCommonInjectionLib::TYPE_MISMATCH);

         case 'interface':
            return (in_array($data, ['helpdesk', 'central'])
                 ?PluginDatainjectionCommonInjectionLib::SUCCESS
                 :PluginDatainjectionCommonInjectionLib::TYPE_MISMATCH);

         default:
            return PluginDatainjectionCommonInjectionLib::SUCCESS;
      }
   }


    /**
    * @see plugins/datainjection/inc/PluginDatainjectionInjectionInterface::addOrUpdateObject()
   **/
   function addOrUpdateObject($values = [], $options = []) {

      $lib = new PluginDatainjectionCommonInjectionLib($this, $values, $options);
      $lib->processAddOrUpdate();
      return $lib->getInjectionResults();
   }

}