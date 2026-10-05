<?php

function generateSummary($result, $bugs, $security, $qualityScore)
{
    $summary = "";

    $summary .= "Project Analysis Summary<br><br>";

    $summary .= "PHP Files: " . $result['php'] . "<br>";
    $summary .= "HTML Files: " . $result['html'] . "<br>";
    $summary .= "CSS Files: " . $result['css'] . "<br>";
    $summary .= "JS Files: " . $result['js'] . "<br>";
    $summary .= "Total Files: " . $result['total'] . "<br><br>";

    $summary .= "Bugs Found: " . count($bugs) . "<br>";
    $summary .= "Security Issues: " . count($security) . "<br>";
    $summary .= "Code Quality Score: " . $qualityScore . "/100<br><br>";

    if($qualityScore >= 80)
    {
        $summary .= "Project quality appears good.";
    }
    elseif($qualityScore >= 60)
    {
        $summary .= "Project quality is average and can be improved.";
    }
    else
    {
        $summary .= "Project requires significant improvements.";
    }

    return $summary;
}

?>