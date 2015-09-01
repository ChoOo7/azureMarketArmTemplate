
$theLocation = "West Europe"
$theName = "testAzureRessourceExample1RG"
$theDeploymentName = "testAzureRessourceExample1DN"
$exempleName = "example2"
$templateFile = $($exempleName+".json")
$templateParametersFile = $($exempleName+".parameters.json")
    
Switch-AzureMode AzureResourceManager
New-AzureResourceGroup -Location $theLocation -Name $theName -DeploymentName $theDeploymentName -TemplateFile $templateFile -TemplateParameterFile $templateParametersFile


