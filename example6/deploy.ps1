$exempleName = "example62"


#Copied from https://github.com/Azure/azure-quickstart-templates/tree/master/201-2-vms-loadbalancer-natrules

$theLocation = "West Europe"
$theName = $("testAzureRessource"+$exempleName+"RG")
$theDeploymentName = $("testAzureRessource"+$exempleName+"DN")
$templateFile = "azuredeploy.json"
$templateParametersFile = "azuredeploy.parameters.json"
    
#Switch-AzureMode AzureResourceManager
New-AzureResourceGroup -Location $theLocation -Name $theName -DeploymentName $theDeploymentName -TemplateFile $templateFile -TemplateParameterFile $templateParametersFile


