$exempleName = "example104"
$appName = $("hackfest"+$exempleName)


$theLocation = "West Europe"
$theName = $("testAzureRessource"+$exempleName+"RG")
$theDeploymentName = $("testAzureRessource"+$exempleName+"DN")
$templateFile = "azuredeploy.json"
$templateParametersFile = "azuredeploy.parameters.json"
    
Switch-AzureMode AzureResourceManager
New-AzureResourceGroup -Location $theLocation -Name $theName
New-AzureResourceGroupDeployment -Name $theDeploymentName -ResourceGroupName $theName -TemplateFile $templateFile -appName $appName -Verbose
