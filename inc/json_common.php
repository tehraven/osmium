<?php
/* Osmium
 * Copyright (C) 2012 Romain "Artefact2" Dalmaso <artefact2@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */


function osmium_get_module_shortlist($shortlist = null) {
  if(!osmium_logged_in()) return array();

  if($shortlist === null) {
    $shortlist = unserialize(osmium_settings_get('shortlist_modules', serialize(array())));
  }
 
  $out = array();
  $rows = array();
  $req = osmium_pg_query_params('SELECT typename, invmodules.typeid FROM osmium.invmodules WHERE invmodules.typeid IN ('.implode(',', $typeids = array_merge(array(-1), $shortlist)).')', array());
  while($row = pg_fetch_row($req)) {
    $rows[$row[1]] = array('typename' => $row[0], 'typeid' => $row[1]);
  }

  $modattr = array();
  osmium_get_attributes_and_effects($typeids, $modattr);
  foreach($rows as &$row) {
    $row['slottype'] = osmium_get_module_slottype($modattr[$row['typeid']]['effects']);
  }

  foreach($shortlist as $typeid) {
    if(!isset($rows[$typeid])) continue;
    $out[] = $rows[$typeid];
  }

  return $out;
}
