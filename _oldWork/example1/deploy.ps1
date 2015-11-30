
Switch-AzureMode AzureResourceManager
New-AzureResourceGroup -Location "West Europe" -locationFromTemplate "West Europe" -Name testAzureRessourceExample1RG -DeploymentName testAzureRessourceExample1DN -TemplateFile R:\srv\hackfest\example1\azuredeploy.json -TemplateParameterFile R:\srv\hackfest\example1\azuredeploy.parameters.json



SYNTAXE
    New-AzureResourceGroup -Name <String> -Location <String> [-DeploymentName <String>] [-Tag <Hashtable[]>] [-Force
    [<SwitchParameter>]] [-TemplateVersion <String>] [-Profile <AzureProfile>] [<CommonParameters>]

    New-AzureResourceGroup -Name <String> -Location <String> [-DeploymentName <String>] [-Tag <Hashtable[]>] [-Force
    [<SwitchParameter>]] -TemplateParameterObject <Hashtable> -TemplateUri <String> [-TemplateVersion <String>]
    [-Profile <AzureProfile>] [<CommonParameters>]

    New-AzureResourceGroup -Name <String> -Location <String> [-DeploymentName <String>] [-Tag <Hashtable[]>] [-Force
    [<SwitchParameter>]] -TemplateParameterObject <Hashtable> -TemplateFile <String> [-StorageAccountName <String>]
    [-TemplateVersion <String>] [-Profile <AzureProfile>] [<CommonParameters>]

    New-AzureResourceGroup -Name <String> -Location <String> [-DeploymentName <String>] [-Tag <Hashtable[]>] [-Force
    [<SwitchParameter>]] -TemplateParameterObject <Hashtable> -GalleryTemplateIdentity <String> [-TemplateVersion
    <String>] [-Profile <AzureProfile>] [<CommonParameters>]

    New-AzureResourceGroup -Name <String> -Location <String> [-DeploymentName <String>] [-Tag <Hashtable[]>] [-Force
    [<SwitchParameter>]] -TemplateParameterFile <String> -GalleryTemplateIdentity <String> [-TemplateVersion <String>]
    [-Profile <AzureProfile>] [<CommonParameters>]

    New-AzureResourceGroup -Name <String> -Location <String> [-DeploymentName <String>] [-Tag <Hashtable[]>] [-Force
    [<SwitchParameter>]] -TemplateParameterFile <String> -TemplateUri <String> [-TemplateVersion <String>] [-Profile
    <AzureProfile>] [<CommonParameters>]

    New-AzureResourceGroup -Name <String> -Location <String> [-DeploymentName <String>] [-Tag <Hashtable[]>] [-Force
    [<SwitchParameter>]] -TemplateParameterFile <String> -TemplateFile <String> [-StorageAccountName <String>]
    [-TemplateVersion <String>] [-Profile <AzureProfile>] [<CommonParameters>]

    New-AzureResourceGroup -Name <String> -Location <String> [-DeploymentName <String>] [-Tag <Hashtable[]>] [-Force
    [<SwitchParameter>]] -GalleryTemplateIdentity <String> [-TemplateVersion <String>] [-Profile <AzureProfile>]
    [<CommonParameters>]

    New-AzureResourceGroup -Name <String> -Location <String> [-DeploymentName <String>] [-Tag <Hashtable[]>] [-Force
    [<SwitchParameter>]] -TemplateFile <String> [-StorageAccountName <String>] [-TemplateVersion <String>] [-Profile
    <AzureProfile>] [<CommonParameters>]

    New-AzureResourceGroup -Name <String> -Location <String> [-DeploymentName <String>] [-Tag <Hashtable[]>] [-Force
    [<SwitchParameter>]] -TemplateUri <String> [-TemplateVersion <String>] [-Profile <AzureProfile>]
    [<CommonParameters>]


-- Suite  --








