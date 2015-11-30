$exempleName = "example87"
$appName = $("hackfest"+$exempleName)


#Copied from https://github.com/Azure/azure-quickstart-templates/tree/master/201-2-vms-loadbalancer-natrules

$theLocation = "West Europe"
$theName = $("testAzureRessource"+$exempleName+"RG")
$theDeploymentName = $("testAzureRessource"+$exempleName+"DN")
$templateFile = "azuredeploy.json"
$templateParametersFile = "azuredeploy.parameters.json"
    
Switch-AzureMode AzureResourceManager
#New-AzureResourceGroup -Location $theLocation -Name $theName -DeploymentName $theDeploymentName -TemplateFile $templateFile -TemplateParameterFile $templateParametersFile -appName $appName
New-AzureResourceGroup -Location $theLocation -Name $theName
New-AzureResourceGroupDeployment -Name $theDeploymentName -ResourceGroupName $theName -TemplateFile $templateFile -appName $appName -Verbose


#Set-AzureVM -ResourceGroupName "testAzureRessourceexample74RG" -Name "haskfestexample74-front1" -Generalized
#PS R:\srv\git\azureMarketArmTemplate\buggyExample> Save-AzureVMImage  -ResourceGroupName "testAzureRessourceexample74RG" -VMName "hackfestexample74-front1" -DestinationContainerName "vhds" -VHDNamePrefix "testtemplate1"