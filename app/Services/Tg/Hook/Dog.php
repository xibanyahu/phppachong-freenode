<?php

declare(strict_types=1);

namespace App\Services\Tg\Hook;

use App\Models\Me as ModelMe;
use App\Models\DogOption as ModelDogOption;
use App\Models\Option as ModelOption;

// $this->oDog->_setDogName("hub"); // 必须，me用，option用
// $this->oDog->_setPrimaryKey("id"); // 可选，默认id
// $this->oDog->_setStrField(["content"]); // 可选，指定长文本字段
// $this->oDog->_setInFormat(["type"]); // 可选，zero^int,^date,^dateFull
// $this->oDog->_setOutFormat(["time" => "date"]); // 可选，date，dateFull

class Dog extends Mod\Base {
    
    public $oModelSelf;
    public $sPrimaryKey = "id";
    public $iStrMaxLen;
    public $aStrField = [];
    public $aInFormat = [];
    public $aOutFormat = [];
    public $sDogName;
    private $aMsg = [];
    public $aSelectField = [];
    public $aHideField = [];
    public $aJsonField = [];

    public function __construct($oModelSelf = false)
    {
        $this->_setModel($oModelSelf);
    }
    
    //// 定义系列
    
    // 必须，关联option和me那些
    public function _setDogName($sDogName)
    {
        $this->sDogName = $sDogName;
    }
    
    // 必须，使用的model
    public function _setModel($oModelSelf)
    {
        $this->oModelSelf = $oModelSelf;
    }
    
    // str字段的最大长度
    public function _setStrMaxLen($iStrMaxLen)
    {
        $this->iStrMaxLen = $iStrMaxLen;
    }
    
    public function _setJsonField($aJsonField)
    {
        $this->aJsonField = $aJsonField;
    }
    
    // 隐藏某些字段[id,name]
    public function _setHideField($aHideField = [])
    {
        $this->aHideField = $aHideField;
    }
    
    // 选择性取某些字段[id,name]
    public function _setSelectField($aSelectField = [])
    {
        $this->aSelectField = $aSelectField;
    }
    
    // 设置长字符串字段
    public function _setStrField($aStrField)
    {
        $this->iStrMaxLen = ModelDogOption::_hit($this->sDogName, "str_len_max");
        
        if (!$this->iStrMaxLen) {
            $this->iStrMaxLen = 60;
            $this->aMsg["警告882"] = "需要设置dog的option的str_len_max，临时设为60。";
        }

        $this->aStrField = $aStrField;
    }
    
    // 设置主键
    public function _setPrimaryKey($key)
    {
        $this->sPrimaryKey = $key;
    }
    
    // 写入数据时格式化某些字段 [transfer_enable => gb]
    public function _setInFormat($aInFormat)
    {
        $this->aInFormat = $aInFormat;
    }
    
    // 输出数据时格式化某些字段 [transfer_enable => gb]
    public function _setOutFormat($aOutFormat)
    {
        $this->aOutFormat = $aOutFormat;
    }
    
    //// 定义系列 end
    
    //// option 系列
    public function option($aParam)
    {
        
        $aOption = ModelDogOption::_list($this->sDogName);
        
        return $this->toJson($aOption);
    }
    
    public function optionAdd($aParam)
    {
        $k = $aParam["aOption"][1] ?? false;
        $v = $aParam["aArg"][1] ?? false;
        
        if ($k == "?") {
            return "#optionAdd {k} {v}";
        }
        
        if (!$k) {
            return "需要k";
        }
        
        if (!$v) {
            return "需要v";
        }
        
        $oOption = new ModelDogOption();
        $oOption->dog_name = $this->sDogName;
        $oOption->k = $k;
        $oOption->v = $v;
        $oOption->save();
        
        return $this->toJson(ModelDogOption::_filter($oOption));
    }
    
    public function optionSet($aParam)
    {
        $k = $aParam["aOption"][1] ?? false;
        $v = $aParam["aArg"][1] ?? false;
        
        if ($v == "?") {
            return "#optionSet__{k} {v}";
        }
        
        if (!$k) {
            return "需要k";
        }
        
        if (!$v) {
            return "需要v";
        }
        
        $oOption = ModelDogOption::where("dog_name", $this->sDogName)->where("k", $k)->first();
        
        if (!$oOption) {
            return "没找到这个";
        }
        
        $oOption->v = $v;
        $oOption->save();
        
        return $this->toJson(ModelDogOption::_filter($oOption));
    }
    
    public function optionDel($aParam)
    {
        $k = $aParam["aArg"][1] ?? false;
        $is_done = $aParam["is_done"] ?? false;
        
        if ($k == "?") {
            return "#optionDel {k}";
        }
        
        if (!$k) {
            return "需要k";
        }
        
        $oOption = ModelDogOption::where("dog_name", $this->sDogName)->where("k", $k)->first();
        
        if (!$oOption) {
            return "没找到这个";
        }
        
        if ($is_done === false) {
            
            $sMsg = "匹配以下，确认用optionDelDone()。";
            $sOption = $this->toJson($oOption);
            
            return $sMsg."\n\n".$sOption;
            
        } else if ($is_done === true) {
            
            $r = $oOption->delete();
            
            return $r;
        }
    }
    
    public function optionDelDone($aParam)
    {
        $aParam["is_done"] = true;
        
        return $this->optionDel($aParam);
    }
    //// option 系列 end
    
    //// 辅助系列
//    private function filterList($oSelfList)
//    {
//        foreach ($oSelfList as &$oSelfListRow) {
//            $oSelfListRow = $this->filterRow($oSelfListRow);
//        }
//
//        return $oSelfList;
//    }
    
    private function meToStr_list($oMe)
    {
        $str = "";
        $i = 0;
        foreach ($oMe as $oMeRow) {
            if ($i !== 0) {
                $str .= "\n\n";
            }
            $str .= $this->meToStr($oMeRow);
            $i++;
        }
        
        return $str;
    }
    
    private function meToStr($oMe)
    {
        $str = $oMe->id.":".$oMe->rag;

        return $str;
    }
    //// 辅助系列 end
    
    //// json系列
//    private function
    //// json系列end
    
    //// me系列
    public function me()
    {
        $oMe = ModelMe::where("class", $this->sDogName)->first();
        
        if (!$oMe) {
            return "空";
        }
        
        return $oMe->rag;
    }
    
    public function meEdit($aParam)
    {
        $aValue = $aParam["aArg"] ?? false;
        
        $sValue = implode(" ", $aValue);
        
        if ($sValue == "?") {
            return "/meEdit {value}";
        }
        
        $aWhere = [
            "class" => $this->sDogName,
        ];

        $aValue = [
            "rag" => $sValue,
        ];

        $oMe = ModelMe::updateOrCreate($aWhere, $aValue);

        return $this->me();
    }
    //// me系列 end
    
//    private function filterRow($oSelfRow)
//    {
//
//        foreach ($this->aStrField as $v) {
//            $oSelfRow->{$v} = $this->subStr($oSelfRow->{$v});
//        }
//
//        foreach ($this->aJsonField as $v) {
//            $oSelfRow->{$v} = json_decode($oSelfRow->{$v});
//        }
//
//        return $oSelfRow;
//    }
    
    private function subStr($input) {
        
        if (!$input) {
            return "";
        }
        
        if (mb_strlen($input, 'UTF-8') > (int) $this->iStrMaxLen) {
            
            return mb_substr($input, 0, (int) $this->iStrMaxLen, 'UTF-8')."……";
            
        }
        
        return $input;
    }

    public function list($aParam)
    {
        return $this->page($aParam);
    }
    
    public function limit($aParam)
    {
        if (($aParam["aArg"][1] ?? false) == "?") {
            return "#limit {skip} {take}";
        }
        
        $iSkip = $aParam["aArg"][1] ?? 0;
        $iTake = $aParam["aArg"][2] ?? false;
        
        if (!$iTake) {
            $iTake = ModelOption::_hit("dog_limit");
        }
        
        if (!$iTake) {
            return "需要option表里的dog_limit";
        }
        
        $oSelf = $this->oModelSelf->skip($iSkip)->take($iTake)->get();
        
        return $this->toJson($oSelf);
    }
    
    public function page($aParam)
    {
        $iPage = $aParam["aArg"][1] ?? 1;
        
        $aParam = [];
        $aParam["aOption"][2] = $iPage;
        
        return $this->find($aParam);
    }

    public function add($aParam)
    {
        $sField = $aParam["aOption"][1] ?? false;
        $sValue = $aParam["aArg"][1] ?? false;
        
        if ($sValue == "?") {
            return "#add__{field} {value}";
        }
        
        if (!$sValue) {
            return "需要value";
        }
        
        if (!$sField) {
            return "需要field";
        }
        
        $this->oModelSelf->$sField = $sValue;
        
        $this->oModelSelf->save();
        
        $aParam = [];
        $aParam["aOption"][1] = $this->sPrimaryKey;
        $aParam["aArg"][1] = $this->oModelSelf->{$this->sPrimaryKey};
        
        return $this->find($aParam);
    }
    
    public function like($aParam)
    {
        
        $aParam["is_like"] = true;
        
        return $this->find($aParam);
        
    }
    
    public function clone($aParam)
    {
        $sWhereValue = $aParam['aArg'][1] ?? false;
        $sWhereField = $aParam["aOption"][1] ?? $this->sPrimaryKey;
        $sWhereFieldType = $aParam["aOption"][2] ?? "int";
        
        if ($sWhereValue == "?") {
            return "#clone__[whereField]__[whereFieldType(:int,str)] [whereValue]";
//            return "#clone__{keyType?int:int,str,int自增str随机} {whereValue}";
        }
        
        if (!$sWhereField) {
            return "需要field";
        }
        
        if (!$sWhereValue) {
            return "需要value";
        }
        
        // 查找文章
        $oModelSelf = $this->oModelSelf->where($sWhereField, $sWhereValue)->first();
        
        if (!$oModelSelf) {
            return "没找到这个";
        }
        
        $oModelSelfNew = $oModelSelf->replicate();
        if ($sWhereFieldType == "str") {
            $oModelSelfNew->$sWhereField = $oModelSelfNew->{$sWhereField}."-clone".rand(0, 9999);
        }
        $oModelSelfNew->save();
        
        $aParam = [];
        $aParam["aOption"][1] = $sWhereField;
        $aParam["aArg"][1] = $oModelSelfNew->$sWhereField;
        
        return $this->find($aParam);
    }
    
    private function _getLimit()
    {
        $iLimit = ModelDogOption::_hit($this->sDogName, "limit");
        
        if (!$iLimit) {
            $iLimit = ModelOption::_hit("dog_limit");
        }
        
        if (!$iLimit) {
//            throw new \Exception("需要option里的dog_limit");
            $iLimit = 30;
            $this->aMsg["警告881"] = "需要option里的dog_limit，临时设为30。";
        }
        
        return $iLimit;
    }
    
    public function find($aParam)
    {
        $sField = $aParam["aOption"][1] ?? $this->sPrimaryKey;
        $sValue = $aParam["aArg"][1] ?? "_all";
        $iPage = $aParam["aOption"][2] ?? 1;
        $isLike = $aParam["is_like"] ?? false;
        $sHitField = $aParam["sHitField"] ?? false;
        
        if ($sValue == "?") {
            return "#find__{field?primaryKey}__{page?1} {value?_all}";
        }
        
        $iLimit = $this->_getLimit();

        if ($sValue === "_all" ) {
            
            $oSelf = $this->oModelSelf;
            
        } else if ($isLike === true) {

            $oSelf = $this->oModelSelf->where($sField, "like", "%".$sValue."%");
            
        } else {

            $oSelf = $this->oModelSelf->where($sField, $sValue);
            
        }

        $oSelf = $oSelf->paginate($iLimit, ['*'], 'page', $iPage);
        
        if ($oSelf->isEmpty()) {
            return "没找到这个";
        }

        // hit必然单体
//        if ($sHitField) {
//            return $oSelf->items()[0]->$sHitField;
//        }
        
        $aData = $this->toDataList($oSelf);
        
        if ($sHitField) {
            if (in_array($sHitField, $this->aStrField)) {
                $aData = $oSelf->items()[0]->$sHitField;
            } else {
                $aData = $aData[0][$sHitField];
            }
        }

        return $this->toJson($aData);
    }
    
    
    private function toDataList($oSelf)
    {
        $aSelf = $oSelf->toArray();

        $aMsg = [];

        // 说明是分页
        if (isset($aSelf["data"])) {
            $aData = $aSelf["data"];
        } else {
            $aData = $aSelf;
        }
        
        $iCount = $aSelf["total"] ?? 0;
        $iPagePer = $aSelf["per_page"] ?? 0;
        $iPage = $aSelf["current_page"] ?? 0;
        $iPageTotal = $aSelf["last_page"] ?? 0;
        
        if ($iPageTotal > 1) {
            if ($iPage) {
                $this->aMsg["页"]["页当前"] = $iPage;
            }
            
            if ($iPageTotal) {
                $this->aMsg["页"]["页总数"] = $iPageTotal;
            }
            
            if ($iPagePer) {
                $this->aMsg["页"]["行每页"] = $iPagePer;
            }
            
            if ($iCount) {
                $this->aMsg["页"]["行总数"] = $iCount;
            }
        }
        
        $aData = $this->filterDataList($aData);
//        $aData = $this->dataInsterMsgList($aData);

        return $aData;
    }
    
    public function toJson($aData)
    {
        if (is_string($aData)) {
            return $aData;
        }

        if ($this->aMsg && !is_string($aData)) {
            if (is_object($aData)) {
                $aData = $aData->toArray();
            }
            array_unshift($aData, $this->aMsg);
        }
        
        return parent::toJson($aData);
    }
    
    private function dataInsterMsgList($aData, $aMsg)
    {
        if ($aMsg) {
            array_unshift($aData, $aMsg);
        }
        
        return $aData;
    }
    
    private function dataInsterMsg($aData, $aMsg)
    {
        $aDataNew = [];
        
        if ($aMsg) {
            $aDataNew[] = $aMsg;
            $aDataNew[] = $aData;
        } else {
            $aDataNew = $aData;
        }
        
        return $aDataNew;
    }
    
    private function toData($oSelf)
    {
        if (!is_array($oSelf)) {
            $aData = $oSelf->toArray();
        } else {
            $aData = $oSelf;
        }
        
        $aData = $this->filterData($aData);
        
        return $aData;
    }
    
    private function filterDataList($aData)
    {
        foreach ($aData as &$aDataRow) {
            $aDataRow = $this->filterData($aDataRow);
        }
        
        return $aData;
    }
    
    private function filterData($aData)
    {
        if ($this->aSelectField) {
            $aDataNew = [];
            foreach ($this->aSelectField as $sSelectField) {
                $aDataNew[$sSelectField] = $aData[$sSelectField];
            }
            $aData = $aDataNew;
        }
        
        if ($this->aHideField) {
            foreach ($this->aHideField as $sHideField) {
                unset($aData[$sHideField]);
            }
        }
        
        foreach ($this->aStrField as $sStrField) {
            $aData[$sStrField] = $this->subStr($aData[$sStrField] ?? '');
        }

        foreach ($this->aJsonField as $sJsonField) {
            $aData[$sJsonField] = json_decode($aData[$sJsonField] ?? '');
        }
        
        foreach ($this->aOutFormat as $sFormatField => $sFormatType) {
            if ($sFormatType == "date") {
                $aData[$sFormatField] = date("Y-m-d", (int) $aData[$sFormatField]);
            } else if ($sFormatType == "dateFull") {
                $aData[$sFormatField] = date("Y-m-d", (int) $aData[$sFormatField]);
            } else if ($sFormatType == "gb") {
                $aData[$sFormatField] = round($aData[$sFormatField] / 1073741824, 2)."gb";
            }
        }
        
        return $aData;
    }
    
    public function kv($aParam)
    {
        $k = $aParam["aOption"][1] ?? false;
        $v = $aParam["aArg"][1] ?? false;
        
        if ($v == "?") {
            return "#kv__{k} {v}";
        }
        
        $aParam = [];
        $aParam["aOption"][1] = $k;
        $aParam["aOption"][2] = "v";
        $aParam["aArg"][1] = $v;
        
        return $this->set($aParam);
    }
    
    public function arrUnset($aParam)
    {
        $aParam["sCmd"] = "unset";
        return $this->arrSet($aParam);
    }
    
    public function arrSet($aParam)
    {
        $sWhereValue = $aParam["aOption"][1] ?? '';
        $sUpdField = $aParam["aOption"][2] ?? '';
        $sUpdKey = $aParam["aOption"][3] ?? '';
        $sUpdValue = $aParam["aArg"][1] ?? '';
        $sCmd = $aParam["sCmd"] ?? 'set';
        
        if ($sUpdValue == "?") {
            return "#arrSet__[sWhereValue]__[sUpdField]__[?sUpdKey] [?sUpdValue]";
        }
        
        if (!$sWhereValue) {
            return "需要whereValue";
        }
        
        if (!$sUpdField) {
            return "需要sWhereValue";
        }
        
        if (!$sUpdKey) {
//            return "需要sUpdKey";
        }
        
        if (!$sUpdValue) {
            if ($sCmd == "set") {
                return "需要sUpdValue";
            }
        }
        
        $oModelSelf = $this->oModelSelf->where($this->sPrimaryKey, $sWhereValue)->first();
        
        if (!$oModelSelf) {
            return "没找到";
        }
        
        $sArr = $oModelSelf->$sUpdField ?? "[]";
        $aArr = json_decode($sArr, true);
        $aArrNew = $aArr;
        
        if ($sCmd == "set") {
            if ($sUpdKey) {
                $aArrNew[$sUpdKey] = $sUpdValue;
            } else {
                $aArrNew[] = $sUpdValue;
            }
        } else if ($sCmd == "unset") {
            if (!$sUpdKey) {
                return "需要sUpdKey";
            }
            if (isset($aArrNew[$sUpdKey])) {
                unset($aArrNew[$sUpdKey]);
            }
        }
        
        $sArrNew = json_encode($aArrNew);
        $oModelSelf->$sUpdField = $sArrNew ?? "[]";
        $r = $oModelSelf->save();
        
        if (!$r) {
            return "save失败？";
        }
        
        $a = json_decode($oModelSelf->$sUpdField);
        
        return $this->toJson($a);
    }
    
    public function schemaAdd($aParam)
    {
        if (($aParam["aArg"][1] ?? false) == "?") {
            return "#jsonAdd {json}";
        }
        
        $aJson = $aParam["aArg"] ?? false;
        $sJson = implode(" ", $aJson);
        
        if (!$sJson) {
            return "需要json";
        }
        
        $aJson = json_decode($sJson, true);
        
        foreach ($aJson as $k => $v) {
            $this->oModelSelf->$k = $this->inFormat($k, $v);
        }
        
        $this->oModelSelf->save();
        
        $aParam = [];
        $aParam["aArg"][1] = $this->oModelSelf->{$this->sPrimaryKey};
        
        return $this->find($aParam);
    }
    
    public function schemaSet($aParam)
    {
        $sWhereValue = $aParam["aOption"][1] ?? false;
        $aJson = $aParam["aArg"] ?? false;
        $sJson = implode(" ", $aJson);
        
        if ($sJson == "?") {
            return "#setJson__{whereValue} {json} | 绑定主键 单体";
        }
        
        if (!$sWhereValue) {
            return "需要whereValue";
        }
        
        if (!$sJson) {
            return "需要json";
        }
        
        $oModelSelf = $this->oModelSelf->where($this->sPrimaryKey, $sWhereValue)->first();
        
        if (!$oModelSelf) {
            return "没找到这个";
        }

        $aJson = json_decode($sJson, true);
        
        foreach ($aJson as $k => $v) {
            $v = $this->inFormat($k, $v);
            $oModelSelf->$k = $v;
        }
        
        $oModelSelf->save();
        
        $aParam = [];
        $aParam["aArg"][1] = $oModelSelf->{$this->sPrimaryKey};
        
        return $this->find($aParam);
    }
    
    public function schemaGet($aParam)
    {
        $sWhereValue = $aParam["aArg"][1] ?? false;
        $sWhereField = $aParam["aOption"][1] ?? $this->sPrimaryKey;
        
        if ($sWhereValue == "?") {
            return "#jsonGet__{whereFile?primary} {whereValue}";
        }
        
        if (!$sWhereValue) {
            return "需要whereValue";
        }
        
        $oSelf = $this->oModelSelf->where($sWhereField, $sWhereValue)->first();
        
        if (!$oSelf) {
            return "没找到这个";
        }
        
        return $this->toJson($oSelf);
    }
    
    public function likeSet($aParam)
    {
        if (($aParam["aArg"][1] ?? false) == "?") {
            return "原set走like匹配，慎用";
        }
        
        $aParam["is_like"] = true;
        
        return $this->set($aParam);
    }
    
    public function likeSetDone($aParam)
    {
        if (($aParam["aArg"][1] ?? false) == "?") {
            return "原set走like匹配，慎用";
        }
        
        $aParam["is_like"] = true;
        $aParam["is_like_done"] = true;
        
        return $this->set($aParam);
    }
    
    public function set($aParam)
    {
        $sSetValue = $aParam["aArg"] ?? false;
        $sSetValue = implode(" ", $sSetValue);
        $bIsLike = $aParam["is_like"] ?? false;
        $bIsLikeDone = $aParam["is_like_done"] ?? false;
        
        if ($sSetValue == "?") {
            $tmp = "#set__{whereField}__{WhereValue}__{setField} {setValue} | 多条";
            $tmp .= "\n\n";
            $tmp .= "#set__{whereValue}__{setField} {setValue} | 主键";
            return $tmp;
        }

        // 有第三参数说明是findSet模式
        if (isset($aParam["aOption"][3])) {
            $sWhereField = $aParam["aOption"][1] ?? false;
            $sWhereValue = $aParam["aOption"][2] ?? false;
            $sSetField = $aParam["aOption"][3] ?? false;
            $sMod = "findSet";
        } else {
            $sWhereField = $this->sPrimaryKey;
            $sWhereValue = $aParam["aOption"][1] ?? false;
            $sSetField = $aParam["aOption"][2] ?? false;
            $sMod = "set";
        }
        
        if (!$sWhereField) {
            return "需要whereField";
        }
        
        if (!$sWhereValue) {
            return "需要whereValue";
        }
        
        if (!$sSetField) {
            return "需要setField";
        }
        
        if ($bIsLike === false) {
            $oSelf = $this->oModelSelf->where($sWhereField, $sWhereValue)->get();
        } else if ($bIsLike === true) {
            
            $oSelf = $this->oModelSelf->where($sWhereField, "like", "%".$sWhereValue."%")->get();
            
            if ($bIsLikeDone === false) {
                
                $sMsg = "匹配以下数据，确认修改用likeSetDone()。";
                
                $sSelfStr = $this->toJson($oSelf);
                
                return $sMsg."\n\n".$sSelfStr;
            }
            
        } else {
            return "??????????????";
        }
        
        if ($oSelf->isEmpty()) {
            return "没找到这个";
        }
        
        $sSetValue = $this->inFormat($sSetField, $sSetValue);
        $i = 0;
        foreach ($oSelf as $oSelfRow) {
            $oSelfRow->$sSetField = $sSetValue;
            $oSelfRow->save();
            $i++;
        }
        
        if ($i == 1) {
            $aParam = [];
            $aParam["aOption"][1] = $this->sPrimaryKey;
            $aParam["aArg"][1] = $oSelfRow->{$this->sPrimaryKey};
            return $this->find($aParam);
        }
        
        return "影响".$i."条数据";
    }
    
    private function inFormat($sSetField, $sSetValue) {
        
        if (in_array($sSetField, $this->aInFormat)) {
            $sSetValue = $this->mapInFormat($sSetValue);
        }
        
        return $sSetValue;
    }
    
    private function mapInFormat($sValue)
    {
        $aValue = explode("^", $sValue, 2);

        if (!isset($aValue[1])) {
            return $sValue;
        }
        
        $sValueQian = $aValue[0];
        $sValueHou = $aValue[1];

        if ($sValueHou == "int") {
            
            if ($sValueQian == "zero") {
                $sValueQian = 0;
            }
            
        } else if ($sValueHou == "dayToDate") {
            
            $sValueQian = date("Y-m-d H:i:s", strtotime("+{$sValueQian} days"));
            
        } else if ($sValueHou == "dateNow") {
            $sValueQian = date("Y-m-d");
        } else if ($sValueHou == "dateTimeNow") {
            $sValueQian = date("Y-m-d H:i:s");
        } else if ($sValueHou == "gb") {
            $sValueQian = $sValueQian * 1073741824;
        }
        
        return $sValueQian;
    }
    
    public function delDone($aParam)
    {
        $aParam["is_done"] = true;
        
        return $this->del($aParam);
    }
    
    public function del($aParam)
    {
        $sField = $aParam["aOption"][1] ?? $this->sPrimaryKey;
        $sValue = $aParam["aArg"][1] ?? false;
        $bIsDone = $aParam["is_done"] ?? false;
        
        if ($sValue == "?") {
            return "#del__{field?默认primaryKey} {value}";
        }
        
        $oSelf = $this->oModelSelf->where($sField, $sValue)->get();
        
        if ($oSelf->isEmpty()) {
            return "没找到这个";
        }
        
        if ($bIsDone == true) {
            
            $i = 0;
            foreach ($oSelf as $oSelfRow) {
                $ii = $oSelfRow->delete();
                $i = $i + $ii;
            }
    
            return "删除".$i."条数据";
        }
        
        $sMsg = "匹配以下数据，确认删除使用delDone()。\n\n";
        $sStr = $this->toJson($oSelf);
        
        return $sMsg.$sStr;
    }
    
    public function hit($aParam)
    {
        $sHitField = $aParam["aOption"][1] ?? false;
        $sWhereField = $aParam["aOption"][2] ?? $this->sPrimaryKey;
        $sWhereValue = $aParam["aArg"][1] ?? false;
        
        if ($sWhereValue == "?") {
            return "#hit__{hitField}__{whereField?primaryKey} {whereValue}";
        }
        
        if (!$sWhereValue) {
            return "需要value";
        }
        
        $aParam = [];
        $aParam["sHitField"] = $sHitField;
        $aParam["aOption"][1] = $sWhereField;
        $aParam["aArg"][1] = $sWhereValue;
        
        return $this->find($aParam);
    }
    
    public function content($aParam)
    {
        $sWhereField = $aParam["aOption"][1] ?? $this->sPrimaryKey;
        $sValue = $aParam["aArg"][1] ?? false;
        
        if ($sValue == "?") {
            return "#content__{whereField?primaryKey} {whereValue}";
        }
        
        if (!$sValue) {
            return "需要value";
        }
        
        $aParam = [];
        $aParam["aOption"][1] = "content";
        $aParam["aOption"][2] = $sWhereField;
        $aParam["aArg"][1] = $sValue;
        
        return $this->hit($aParam);
    }
    
    public function table()
    {
        $sTable = $this->oModelSelf->getTable();

        $columns = \DB::select("SHOW CREATE TABLE `{$sTable}`");
        
        return $columns[0]->{"Create Table"};
    }
    
}
