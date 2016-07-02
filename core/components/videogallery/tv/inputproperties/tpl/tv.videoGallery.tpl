<div id="tv-input-properties-form{$tv}"></div>
{literal}

<script type="text/javascript">
// <![CDATA[
var params = {
{/literal}{foreach from=$params key=k item=v name='p'}
 '{$k}': '{$v|escape:"javascript"}'{if NOT $smarty.foreach.p.last},{/if}
{/foreach}{literal}
};
var oc = {'change':{fn:function(){Ext.getCmp('modx-panel-tv').markDirty();},scope:this}};
MODx.load({
    xtype: 'panel'
    ,layout: 'form'
    ,autoHeight: true
    ,cls: 'form-with-labels'
    ,border: false
    ,labelAlign: 'top'
    ,items: [{
        xtype: 'combo-boolean'
        ,fieldLabel: _('required')
        ,description: MODx.expandHelp ? '' : _('required_desc')
        ,name: 'inopt_allowBlank'
        ,hiddenName: 'inopt_allowBlank'
        ,id: 'inopt_allowBlank{/literal}{$tv}{literal}'
        ,value: !(params['allowBlank'] == 0 || params['allowBlank'] == 'false')
        ,width: 200
        ,listeners: oc
    },{
        xtype: MODx.expandHelp ? 'label' : 'hidden'
        ,forId: 'inopt_allowBlank{/literal}{$tv}{literal}'
        ,html: _('required_desc')
        ,cls: 'desc-under'
    },/*{
        xtype: 'textfield'
        ,fieldLabel: '{/literal}{$vglex.admin_coords}{literal}'
        ,description: MODx.expandHelp ? '' : '{/literal}{$vglex.admin_coords_desc}{literal}'
        ,name: 'inopt_adminCoords'
        ,id: 'inopt_adminCoords{/literal}{$tv}{literal}'
        ,value: params['adminCoords'] || ''
        ,width: '99%'
        ,listeners: oc
    },{
        xtype: MODx.expandHelp ? 'label' : 'hidden'
        ,forId: 'inopt_adminCoords{/literal}{$tv}{literal}'
        ,html: '{/literal}{$vglex.admin_coords_desc}{literal}'
        ,cls: 'desc-under'
    },{
        xtype: 'textfield'
        ,fieldLabel: '{/literal}{$vglex.admin_zoom}{literal}'
        ,description: MODx.expandHelp ? '' : '{/literal}{$vglex.admin_zoom_desc}{literal}'
        ,name: 'inopt_adminZoom'
        ,id: 'inopt_adminZoom{/literal}{$tv}{literal}'
        ,value: params['adminZoom'] || ''
        ,width: '99%'
        ,listeners: oc
    },{
        xtype: MODx.expandHelp ? 'label' : 'hidden'
        ,forId: 'inopt_adminZoom{/literal}{$tv}{literal}'
        ,html: '{/literal}{$vglex.admin_zoom_desc}{literal}'
        ,cls: 'desc-under'
    }*/]
    ,renderTo: 'tv-input-properties-form{/literal}{$tv}{literal}'
});
// ]]>
</script>
{/literal}
