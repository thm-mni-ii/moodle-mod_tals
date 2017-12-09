//
//  DetailViewController.h
//  Anwesenheit
//
//  Created by Sarah B on 23.11.17.
//  Copyright © 2017 THM Gießen. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface DetailViewController : UIViewController

@property(strong, nonatomic) NSArray *detailModal;
@property (weak, nonatomic) IBOutlet UITextView *detailDescription;
@property (weak, nonatomic) IBOutlet UITextView *detailActive;
@property (weak, nonatomic) IBOutlet UITextField *detailPinEntry;
@property (weak, nonatomic) IBOutlet UILabel *detailTitel;
@property (weak, nonatomic) IBOutlet UILabel *detailPIN;
- (IBAction)enterPIN:(id)sender;

@end
