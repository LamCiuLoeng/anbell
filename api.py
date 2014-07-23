# -*- coding: utf-8 -*-
import urllib, urllib2, json, codecs, random

def ecsape_bom( s ):
    return s[3:] if s[:3] == codecs.BOM_UTF8 else s


def get_result( url, data ):
    result = urllib2.urlopen( url, urllib.urlencode( data ) )
    print '%s?%s' % ( url, urllib.urlencode( data ) )
    k = ecsape_bom( ''.join( result.readlines() ) )
    return json.loads( k )


def test():
    #===========================================================================
    # login to the server
    #===========================================================================
    login_url = 'http://localhost:90/index.php/API/v1/login'
    param = {
             'system_no' : '10000002' , 'password' : '102',
             }

    user = get_result( login_url, param )
    assert user['flag'] == 0
    assert 'user' in user
    assert user['user_key'] is not None
    print '---pass login'

    user_key = user['user_key']

    #===========================================================================
    # get user info
    #===========================================================================
    user_url = 'http://localhost:90/index.php/API/v1/action'
    param = {
             '_q' : 'get_user_info',
             'user_key' : user_key,
             'user_id'  : user['user']['id'],
             }
    info = get_result( user_url, param )
    assert info['flag'] == 0
    assert 'data' in info
    print '---pass get_user_info'


    #===========================================================================
    # get user's plan
    #===========================================================================
    plan_url = 'http://localhost:90/index.php/API/v1/action'
    param = {
             '_q' : 'get_plan_by_user',
             'user_key' : user_key,
             'user_id'  : user['user']['id'],
             }

    plan = get_result( plan_url, param )
    assert plan['flag'] == 0
    assert 'data' in plan
    assert 'courseware' in plan['data']
    assert type( plan['data']['courseware'] ) == list
    assert 'game' in plan['data']
    assert type( plan['data']['game'] ) == list
    print '---pass get_plan_by_user'

    #===========================================================================
    # save user's data
    #===========================================================================
    save_url = 'http://localhost:90/index.php/API/v1/action'
    param1 = {
             '_q' : 'save_user_data',
             'user_key' : user_key,
             'user_id'  : user['user']['id'],
             'data_type' : 'C',
             'obj_id' : 1,
             }
    save1 = get_result( save_url, param1 )
    assert save1['flag'] == 0
    print '---pass save_user_data c'

    param2 = {
             '_q' : 'save_user_data',
             'user_key' : user_key,
             'user_id'  : user['user']['id'],
             'data_type' : 'G',
             'obj_id' : 1,
             'score'  : random.randint( 10, 100 ),
             }
    save1 = get_result( save_url, param2 )
    assert save1['flag'] == 0
    print '---pass save_user_data g'

    param3 = {
             '_q' : 'get_questions',
             'category_id' : 1,
             'user_key' : user_key,
             }
    qs = get_result( save_url, param3 )
    assert qs['flag'] == 0
    assert 'data' in qs
    assert type( qs['data'] ) == list
    if( len( qs['data'] ) > 0 ):
        assert 'content' in  qs['data'][0]
        assert 'correct_answer' in  qs['data'][0]
    print '---pass get_questions g'
    print 'finished'

if __name__ == "__main__":
    test()
