//
//  LoginViewController.m
//  Anwesenheit
//
//  Created by Sarah B on 05.12.17.
//  Copyright © 2017 THM Gießen. All rights reserved.
//

#import "LoginViewController.h"
#import "AppData.h"

@interface LoginViewController ()

@property (nonatomic, strong) NSURLConnection *connection;

@end

@implementation LoginViewController

- (void)viewDidLoad {
    [super viewDidLoad];
    // Do any additional setup after loading the view.
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

-(void) doLogin{
    // Request Token from server
    username = self.UserName.text;
    password = self.Password.text;
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    if(self.switchWay.isOn){
        [defaults setBool:YES forKey:@"checkLogged"];
    }
    [AppData getToken:username password:password token:^(TokenObject *token){
        if(token.checkLogged){
            NSString * storyboardName = @"Main";
            UIStoryboard *storyboard = [UIStoryboard storyboardWithName:storyboardName bundle: nil];
            UIViewController * vc = [storyboard instantiateViewControllerWithIdentifier:@"initialScreen"];
            [self presentViewController:vc animated:YES completion:nil];}
    }];
}

/*
#pragma mark - Navigation

// In a storyboard-based application, you will often want to do a little preparation before navigation
- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender {
    // Get the new view controller using [segue destinationViewController].
    // Pass the selected object to the new view controller.
}
*/

- (BOOL)textFieldShouldReturn:(UITextField *)theTextField {
    if (theTextField == self.Password) {
        [theTextField resignFirstResponder];
    } else if (theTextField == self.UserName) {
        [self.Password becomeFirstResponder];
    }
    return YES;
}

- (IBAction)signIn:(id)sender {
    [self doLogin];
}


- (IBAction)uName:(id)sender {
}

- (IBAction)pWord:(id)sender {
    [self doLogin];
}

- (IBAction)stayLoggedIn:(id)sender {
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    if(self.switchWay.isOn){
        [defaults setBool:YES forKey:@"checkLogged"];
    }
    else{
        [defaults setBool:NO forKey:@"checkLogged"];
    }
    
    
}
@end
