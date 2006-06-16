/*
**  $Id: JQConst.java,v 1.1 2006/06/16 14:16:35 goneri Exp $
**
**  Copyright (C) 2006 ESME SUDRIA ( www.esme.fr ) 
**
**  Authors: 
**	BOUCHER Nicolas <shoub_n@hotmail.com>
**  	MODELIN Maxence  <maxence_modelin@hotmail.com>
**  	MULOT Louis <vindic@noos.fr>
**
**  This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
**  the Free Software Foundation; either version 2 of the License, or
**  (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
**  but WITHOUT ANY WARRANTY; without even the implied warranty of
**  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
**  GNU General Public License for more details.
**
**  You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
**
**
*/
package org.qsos.data;

import org.eclipse.swt.graphics.Color;


/**
 * @author MULOT_L
 *
 * This class is gather the constants of the JQ application.
 */
public class JQConst
{
	/**
	 * This class contains constants for the JQ application
	 */
	
	
	public static final String SCORE_0 = "0";
	public static final String SCORE_1 = "1";
	public static final String SCORE_2 = "2";
	
	
	
	// Column constants
	public static final int COLUMN_TITLE = 0;
	public static final int COLUMN_DESC  = 1;
	public static final int COLUMN_COMMENT = 2;
	public static final int COLUMN_SCORE = 3;
	public static final int MAX_COLUMN_NUMBER = 4;
	
	
	public static final int DENOMINATOR_WIDTH_COLUMN_TITLE = 4;
	public static final int DENOMINATOR_WIDTH_COLUMN_DESC  = 3;
	public static final int DENOMINATOR_WIDTH_COLUMN_COMMENT = 3;
	public static final int DENOMINATOR_WIDTH_COLUMN_SCORE = 16;

	
	// Set color
	public static final Color backGround_JQEditor = new Color(null, 196, 202, 218);
	public static final Color backGround_JQEditor2 = new Color(null, 188, 208, 235);
	public static final Color backGround_JQEditor3 = new Color(null, 148, 182, 231);
	public static final Color otherColor  = new Color(null, 50, 50, 50);
	
	
	public static final int headerGroupNumber = 0;
	
	
	
	// KeyCode for KeyListener
	public static final int TAB_KEYCODE = 9;
	public static final int ENTER_KEYCODE = 13;
	public static final int SPACE_KEYCODE = 32;
	public static final int UP_KEYCODE = 16777217;
	public static final int DOWN_KEYCODE = 16777218;
	public static final int LEFT_KEYCODE = 16777219;
	public static final int RIGHT_KEYCODE = 16777220;
	public static final int KEYCODE_0 = 224;
	public static final int KEYCODE_0_BIS = 16777264;
	public static final int KEYCODE_1 = 38;
	public static final int KEYCODE_1_BIS = 16777265;
	public static final int KEYCODE_2 = 233;
	public static final int KEYCODE_2_BIS = 16777266;
	public static final int ENTER_KEYCODE2 = 16777296;
	public static final int KEYCODE_ESCAPE = 27;
	
	
	// Constants used to generate the Radar-like chart
	
	public static final java.awt.Color RADAR_SERIES_PAINT = new java.awt.Color(255,132,0);
	public static final double RADAR_MAX_VALUE = 2.0;
	public static final double RADAR_AXIS_LABEL_GAP = 0.05;
	public static final double RADAR_HEAD_PERCENT = 0.03;
	public static final double RADAR_INTERIOR_GAP = 0.25;
	public static final String RADAR_SPIDERWEB_4 = "images/Radar/radar4.png"; 
	public static final String RADAR_SPIDERWEB_5 = "images/Radar/radar5.png";
	public static final String RADAR_SPIDERWEB_6 = "images/Radar/radar6.png"; 
	public static final String RADAR_SPIDERWEB_7 = "images/Radar/radar7.png";

}
