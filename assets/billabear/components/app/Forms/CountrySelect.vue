<template>
  <select id="country-select" class="form-field"  v-model="localValue" @change="$emit('update:modelValue', $event.target.value)">
    <option value="">{{ $t('global.select_country') }}</option>
    <optgroup label="Popular">
      <option value="AU">{{ $t('global.country.AU') }}</option>
      <option value="BE">{{ $t('global.country.BE') }}</option>
      <option value="CA">{{ $t('global.country.CA') }}</option>
      <option value="HR">{{ $t('global.country.HR') }}</option>
      <option value="CZ">{{ $t('global.country.CZ') }}</option>
      <option value="DK">{{ $t('global.country.DK') }}</option>
      <option value="EE">{{ $t('global.country.EE') }}</option>
      <option value="FI">{{ $t('global.country.FI') }}</option>
      <option value="FR">{{ $t('global.country.FR') }}</option>
      <option value="DE">{{ $t('global.country.DE') }}</option>
      <option value="GR">{{ $t('global.country.GR') }}</option>
      <option value="HU">{{ $t('global.country.HU') }}</option>
      <option value="IS">{{ $t('global.country.IS') }}</option>
      <option value="LV">{{ $t('global.country.LV') }}</option>
      <option value="LI">{{ $t('global.country.LI') }}</option>
      <option value="LT">{{ $t('global.country.LT') }}</option>
      <option value="LU">{{ $t('global.country.LU') }}</option>
      <option value="GB">{{ $t('global.country.GB') }}</option>
      <option value="US">{{ $t('global.country.US') }}</option>
      <option value="NL">{{ $t('global.country.NL') }}</option>
      <option value="RO">{{ $t('global.country.RO') }}</option>
      <option value="SK">{{ $t('global.country.SK') }}</option>
      <option value="SI">{{ $t('global.country.SI') }}</option>
      <option value="ES">{{ $t('global.country.ES') }}</option>
      <option value="SE">{{ $t('global.country.SE') }}</option>
    </optgroup>
    <optgroup label="Rest">
      <option value="AF">{{ $t('global.country.AF') }}</option>
      <option value="AL">{{ $t('global.country.AL') }}</option>
      <option value="DZ">{{ $t('global.country.DZ') }}</option>
      <option value="AD">{{ $t('global.country.AD') }}</option>
      <option value="AO">{{ $t('global.country.AO') }}</option>
      <option value="AI">{{ $t('global.country.AI') }}</option>
      <option value="AQ">{{ $t('global.country.AQ') }}</option>
      <option value="AG">{{ $t('global.country.AG') }}</option>
      <option value="AR">{{ $t('global.country.AR') }}</option>
      <option value="AM">{{ $t('global.country.AM') }}</option>
      <option value="AW">{{ $t('global.country.AW') }}</option>
      <option value="AT">{{ $t('global.country.AT') }}</option>
      <option value="AZ">{{ $t('global.country.AZ') }}</option>
      <option value="BS">{{ $t('global.country.BS') }}</option>
      <option value="BH">{{ $t('global.country.BH') }}</option>
      <option value="BD">{{ $t('global.country.BD') }}</option>
      <option value="BB">{{ $t('global.country.BB') }}</option>
      <option value="BY">{{ $t('global.country.BY') }}</option>
      <option value="BZ">{{ $t('global.country.BZ') }}</option>
      <option value="BJ">{{ $t('global.country.BJ') }}</option>
      <option value="BM">{{ $t('global.country.BM') }}</option>
      <option value="BT">{{ $t('global.country.BT') }}</option>
      <option value="BO">{{ $t('global.country.BO') }}</option>
      <option value="BA">{{ $t('global.country.BA') }}</option>
      <option value="BW">{{ $t('global.country.BW') }}</option>
      <option value="BR">{{ $t('global.country.BR') }}</option>
      <option value="IO">{{ $t('global.country.IO') }}</option>
      <option value="BN">{{ $t('global.country.BN') }}</option>
      <option value="BG">{{ $t('global.country.BG') }}</option>
      <option value="BF">{{ $t('global.country.BF') }}</option>
      <option value="BI">{{ $t('global.country.BI') }}</option>
      <option value="CV">{{ $t('global.country.CV') }}</option>
      <option value="KH">{{ $t('global.country.KH') }}</option>
      <option value="CM">{{ $t('global.country.CM') }}</option>
      <option value="KY">{{ $t('global.country.KY') }}</option>
      <option value="CF">{{ $t('global.country.CF') }}</option>
      <option value="TD">{{ $t('global.country.TD') }}</option>
      <option value="CL">{{ $t('global.country.CL') }}</option>
      <option value="CN">{{ $t('global.country.CN') }}</option>
      <option value="CX">{{ $t('global.country.CX') }}</option>
      <option value="CC">{{ $t('global.country.CC') }}</option>
      <option value="CO">{{ $t('global.country.CO') }}</option>
      <option value="KM">{{ $t('global.country.KM') }}</option>
      <option value="CG">{{ $t('global.country.CG') }}</option>
      <option value="CD">{{ $t('global.country.CD') }}</option>
      <option value="CK">{{ $t('global.country.CK') }}</option>
      <option value="CR">{{ $t('global.country.CR') }}</option>
      <option value="CI">{{ $t('global.country.CI') }}</option>
      <option value="CU">{{ $t('global.country.CU') }}</option>
      <option value="CY">{{ $t('global.country.CY') }}</option>
      <option value="DJ">{{ $t('global.country.DJ') }}</option>
      <option value="DM">{{ $t('global.country.DM') }}</option>
      <option value="DO">{{ $t('global.country.DO') }}</option>
      <option value="EC">{{ $t('global.country.EC') }}</option>
      <option value="EG">{{ $t('global.country.EG') }}</option>
      <option value="SV">{{ $t('global.country.SV') }}</option>
      <option value="GQ">{{ $t('global.country.GQ') }}</option>
      <option value="ER">{{ $t('global.country.ER') }}</option>
      <option value="SZ">{{ $t('global.country.SZ') }}</option>
      <option value="ET">{{ $t('global.country.ET') }}</option>
      <option value="FK">{{ $t('global.country.FK') }}</option>
      <option value="FO">{{ $t('global.country.FO') }}</option>
      <option value="FJ">{{ $t('global.country.FJ') }}</option>
      <option value="GF">{{ $t('global.country.GF') }}</option>
      <option value="PF">{{ $t('global.country.PF') }}</option>
      <option value="GA">{{ $t('global.country.GA') }}</option>
      <option value="GM">{{ $t('global.country.GM') }}</option>
      <option value="GE">{{ $t('global.country.GE') }}</option>
      <option value="GH">{{ $t('global.country.GH') }}</option>
      <option value="GI">{{ $t('global.country.GI') }}</option>
      <option value="GL">{{ $t('global.country.GL') }}</option>
      <option value="GD">{{ $t('global.country.GD') }}</option>
      <option value="GP">{{ $t('global.country.GP') }}</option>
      <option value="GT">{{ $t('global.country.GT') }}</option>
      <option value="GG">{{ $t('global.country.GG') }}</option>
      <option value="GN">{{ $t('global.country.GN') }}</option>
      <option value="GW">{{ $t('global.country.GW') }}</option>
      <option value="GY">{{ $t('global.country.GY') }}</option>
      <option value="HT">{{ $t('global.country.HT') }}</option>
      <option value="HN">{{ $t('global.country.HN') }}</option>
      <option value="HK">{{ $t('global.country.HK') }}</option>
      <option value="IN">{{ $t('global.country.IN') }}</option>
      <option value="ID">{{ $t('global.country.ID') }}</option>
      <option value="IR">{{ $t('global.country.IR') }}</option>
      <option value="IQ">{{ $t('global.country.IQ') }}</option>
      <option value="IE">{{ $t('global.country.IE') }}</option>
      <option value="IM">{{ $t('global.country.IM') }}</option>
      <option value="IL">{{ $t('global.country.IL') }}</option>
      <option value="IT">{{ $t('global.country.IT') }}</option>
      <option value="JM">{{ $t('global.country.JM') }}</option>
      <option value="JP">{{ $t('global.country.JP') }}</option>
      <option value="JE">{{ $t('global.country.JE') }}</option>
      <option value="JO">{{ $t('global.country.JO') }}</option>
      <option value="KZ">{{ $t('global.country.KZ') }}</option>
      <option value="KE">{{ $t('global.country.KE') }}</option>
      <option value="KI">{{ $t('global.country.KI') }}</option>
      <option value="KP">{{ $t('global.country.KP') }}</option>
      <option value="KR">{{ $t('global.country.KR') }}</option>
      <option value="KW">{{ $t('global.country.KW') }}</option>
      <option value="KG">{{ $t('global.country.KG') }}</option>
      <option value="LA">{{ $t('global.country.LA') }}</option>
      <option value="LB">{{ $t('global.country.LB') }}</option>
      <option value="LS">{{ $t('global.country.LS') }}</option>
      <option value="LR">{{ $t('global.country.LR') }}</option>
      <option value="LY">{{ $t('global.country.LY') }}</option>
      <option value="MO">{{ $t('global.country.MO') }}</option>
      <option value="MG">{{ $t('global.country.MG') }}</option>
      <option value="MW">{{ $t('global.country.MW') }}</option>
      <option value="MY">{{ $t('global.country.MY') }}</option>
      <option value="MV">{{ $t('global.country.MV') }}</option>
      <option value="ML">{{ $t('global.country.ML') }}</option>
      <option value="MT">{{ $t('global.country.MT') }}</option>
      <option value="MH">{{ $t('global.country.MH') }}</option>
      <option value="MQ">{{ $t('global.country.MQ') }}</option>
      <option value="MR">{{ $t('global.country.MR') }}</option>
      <option value="MU">{{ $t('global.country.MU') }}</option>
      <option value="YT">{{ $t('global.country.YT') }}</option>
      <option value="MX">{{ $t('global.country.MX') }}</option>
      <option value="FM">{{ $t('global.country.FM') }}</option>
      <option value="MD">{{ $t('global.country.MD') }}</option>
      <option value="MC">{{ $t('global.country.MC') }}</option>
      <option value="MN">{{ $t('global.country.MN') }}</option>
      <option value="ME">{{ $t('global.country.ME') }}</option>
      <option value="MS">{{ $t('global.country.MS') }}</option>
      <option value="MA">{{ $t('global.country.MA') }}</option>
      <option value="MZ">{{ $t('global.country.MZ') }}</option>
      <option value="MM">{{ $t('global.country.MM') }}</option>
      <option value="NA">{{ $t('global.country.NA') }}</option>
      <option value="NR">{{ $t('global.country.NR') }}</option>
      <option value="NP">{{ $t('global.country.NP') }}</option>
      <option value="NC">{{ $t('global.country.NC') }}</option>
      <option value="NZ">{{ $t('global.country.NZ') }}</option>
      <option value="NI">{{ $t('global.country.NI') }}</option>
      <option value="NE">{{ $t('global.country.NE') }}</option>
      <option value="NG">{{ $t('global.country.NG') }}</option>
      <option value="NU">{{ $t('global.country.NU') }}</option>
      <option value="NF">{{ $t('global.country.NF') }}</option>
      <option value="MK">{{ $t('global.country.MK') }}</option>
      <option value="NO">{{ $t('global.country.NO') }}</option>
      <option value="OM">{{ $t('global.country.OM') }}</option>
      <option value="PK">{{ $t('global.country.PK') }}</option>
      <option value="PW">{{ $t('global.country.PW') }}</option>
      <option value="PS">{{ $t('global.country.PS') }}</option>
      <option value="PA">{{ $t('global.country.PA') }}</option>
      <option value="PG">{{ $t('global.country.PG') }}</option>
      <option value="PY">{{ $t('global.country.PY') }}</option>
      <option value="PE">{{ $t('global.country.PE') }}</option>
      <option value="PH">{{ $t('global.country.PH') }}</option>
      <option value="PN">{{ $t('global.country.PN') }}</option>
      <option value="PL">{{ $t('global.country.PL') }}</option>
      <option value="PT">{{ $t('global.country.PT') }}</option>
      <option value="QA">{{ $t('global.country.QA') }}</option>
      <option value="RE">{{ $t('global.country.RE') }}</option>
      <option value="RU">{{ $t('global.country.RU') }}</option>
      <option value="RW">{{ $t('global.country.RW') }}</option>
      <option value="BL">{{ $t('global.country.BL') }}</option>
      <option value="SH">{{ $t('global.country.SH') }}</option>
      <option value="KN">{{ $t('global.country.KN') }}</option>
      <option value="LC">{{ $t('global.country.LC') }}</option>
      <option value="MF">{{ $t('global.country.MF') }}</option>
      <option value="PM">{{ $t('global.country.PM') }}</option>
      <option value="VC">{{ $t('global.country.VC') }}</option>
      <option value="WS">{{ $t('global.country.WS') }}</option>
      <option value="SM">{{ $t('global.country.SM') }}</option>
      <option value="ST">{{ $t('global.country.ST') }}</option>
      <option value="SA">{{ $t('global.country.SA') }}</option>
      <option value="SN">{{ $t('global.country.SN') }}</option>
      <option value="RS">{{ $t('global.country.RS') }}</option>
      <option value="SC">{{ $t('global.country.SC') }}</option>
      <option value="SL">{{ $t('global.country.SL') }}</option>
      <option value="SG">{{ $t('global.country.SG') }}</option>
      <option value="SX">{{ $t('global.country.SX') }}</option>
      <option value="SB">{{ $t('global.country.SB') }}</option>
      <option value="SO">{{ $t('global.country.SO') }}</option>
      <option value="ZA">{{ $t('global.country.ZA') }}</option>
      <option value="GS">{{ $t('global.country.GS') }}</option>
      <option value="SS">{{ $t('global.country.SS') }}</option>
      <option value="LK">{{ $t('global.country.LK') }}</option>
      <option value="SD">{{ $t('global.country.SD') }}</option>
      <option value="SR">{{ $t('global.country.SR') }}</option>
      <option value="SJ">{{ $t('global.country.SJ') }}</option>
      <option value="CH">{{ $t('global.country.CH') }}</option>
      <option value="SY">{{ $t('global.country.SY') }}</option>
      <option value="TW">{{ $t('global.country.TW') }}</option>
      <option value="TJ">{{ $t('global.country.TJ') }}</option>
      <option value="TZ">{{ $t('global.country.TZ') }}</option>
      <option value="TH">{{ $t('global.country.TH') }}</option>
      <option value="TL">{{ $t('global.country.TL') }}</option>
      <option value="TG">{{ $t('global.country.TG') }}</option>
      <option value="TK">{{ $t('global.country.TK') }}</option>
      <option value="TO">{{ $t('global.country.TO') }}</option>
      <option value="TT">{{ $t('global.country.TT') }}</option>
      <option value="TN">{{ $t('global.country.TN') }}</option>
      <option value="TR">{{ $t('global.country.TR') }}</option>
      <option value="TM">{{ $t('global.country.TM') }}</option>
      <option value="TC">{{ $t('global.country.TC') }}</option>
      <option value="TV">{{ $t('global.country.TV') }}</option>
      <option value="UG">{{ $t('global.country.UG') }}</option>
      <option value="UA">{{ $t('global.country.UA') }}</option>
      <option value="AE">{{ $t('global.country.AE') }}</option>
      <option value="UM">{{ $t('global.country.UM') }}</option>
      <option value="UY">{{ $t('global.country.UY') }}</option>
      <option value="UZ">{{ $t('global.country.UZ') }}</option>
      <option value="VU">{{ $t('global.country.VU') }}</option>
      <option value="VE">{{ $t('global.country.VE') }}</option>
      <option value="VN">{{ $t('global.country.VN') }}</option>
      <option value="VG">{{ $t('global.country.VG') }}</option>
      <option value="VI">{{ $t('global.country.VI') }}</option>
      <option value="WF">{{ $t('global.country.WF') }}</option>
      <option value="EH">{{ $t('global.country.EH') }}</option>
      <option value="YE">{{ $t('global.country.YE') }}</option>
      <option value="ZM">{{ $t('global.country.ZM') }}</option>
      <option value="ZW">{{ $t('global.country.ZW') }}</option>
    </optgroup>
  </select>

</template>

<script>
export default {
  name: "CountrySelect",
  props: ['modelValue'],
  data() {
    return {
      localValue: this.modelValue,
    }
  },
}
</script>

<style scoped>

</style>
