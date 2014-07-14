# -*- coding: utf-8 -*-
'''
###########################################
#  Created on 2014-7-13
#  @author: cl.lam <lamciuloeng@gmail.com>
#  Description:
###########################################
'''
from datetime import datetime as dt
from sqlalchemy import create_engine, Column, ForeignKey, Table
from sqlalchemy.types import Integer, DateTime, Text, Unicode, Float
from sqlalchemy.orm import scoped_session, sessionmaker, relation, backref, synonym
from sqlalchemy.ext.declarative import declarative_base


SQLALCHEMY_DATABASE_URI = "mysql://root:root@127.0.0.1:3306/anbels?charset=utf8"

engine = create_engine( SQLALCHEMY_DATABASE_URI, echo = False, pool_size = 20 )
maker = sessionmaker( bind = engine, autoflush = True, autocommit = False )
DBSession = scoped_session( maker )
DeclarativeBase = declarative_base()
metadata = DeclarativeBase.metadata

# DBSession.configure(bind = engine)

#===============================================================================
# model define
#===============================================================================

class SysMixin( object ):
    create_time = Column( DateTime, default = dt.now )
    create_by_id = Column( Integer, default = 1 )
    update_time = Column( DateTime, default = dt.now, onupdate = dt.now )
    update_by_id = Column( Integer, default = 1 )
    active = Column( Integer, default = 0 )  # 0 is active ,1 is inactive


#===============================================================================
# auth
#===============================================================================
group_permission_table = Table( 'anbels_auth_group_permission', metadata,
    Column( 'group_id', Integer, ForeignKey( 'anbels_auth_group.id',
        onupdate = "CASCADE", ondelete = "CASCADE" ), primary_key = True ),
    Column( 'permission_id', Integer, ForeignKey( 'anbels_auth_permission.id',
        onupdate = "CASCADE", ondelete = "CASCADE" ), primary_key = True )
 )

user_group_table = Table( 'anbels_auth_user_group', metadata,
    Column( 'user_id', Integer, ForeignKey( 'anbels_auth_user.id',
        onupdate = "CASCADE", ondelete = "CASCADE" ), primary_key = True ),
    Column( 'group_id', Integer, ForeignKey( 'anbels_auth_group.id',
        onupdate = "CASCADE", ondelete = "CASCADE" ), primary_key = True )
 )


class User( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_auth_user'

    id = Column( Integer, autoincrement = True, primary_key = True )
    name = Column( Unicode( 1000 ), nullable = False )
    password = Column( Unicode( 1000 ), nullable = False )
    last_login_time = Column( DateTime )

class Group( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_auth_group'

    id = Column( Integer, autoincrement = True, primary_key = True )
    name = Column( Unicode( 1000 ), nullable = False )
    display_name = Column( Unicode( 1000 ) )
    users = relation( 'User', secondary = user_group_table, backref = 'groups' )


class Permission( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_auth_permission'

    id = Column( Integer, autoincrement = True, primary_key = True )
    name = Column( Unicode( 1000 ), nullable = False )


#===============================================================================
# master
#===============================================================================
class Location( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_master_location'

    id = Column( Integer, autoincrement = True, primary_key = True )
    name = Column( Unicode( 1000 ) )
    full_name = Column( Text )
    full_path_ids = Column( Text )
    parent_id = Column( Integer, default = None )

    def __str__( self ): return self.full_name
    def __repr__( self ): return self.full_name
    def __unicode__( self ): return self.full_name



class Category( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_master_category'

    id = Column( Integer, autoincrement = True, primary_key = True )
    name = Column( Unicode( 1000 ) )
    desc = Column( Text )

#===============================================================================
# logic
#===============================================================================
class School( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_logic_school'

    id = Column( Integer, autoincrement = True, primary_key = True )
    name = Column( Unicode( 1000 ), nullable = False )
    location_id = Column( Integer, ForeignKey( 'anbels_master_location.id' ) )
    desc = Column( Text )


class_user_table = Table( 'anbels_logic_class_user', metadata,
    Column( 'user_id', Integer, ForeignKey( 'anbels_auth_user.id',
        onupdate = "CASCADE", ondelete = "CASCADE" ), primary_key = True ),
    Column( 'class_id', Integer, ForeignKey( 'anbels_logic_class.id',
        onupdate = "CASCADE", ondelete = "CASCADE" ), primary_key = True ),
    Column( 'role', Unicode( 5 ), )  # T is teacher , S is sudent
 )


class Class( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_logic_class'

    id = Column( Integer, autoincrement = True, primary_key = True )
    school_id = Column( Integer, ForeignKey( 'anbels_logic_school.id' ) )
    grade = Column( Integer, default = 1 )
    name = Column( Unicode( 1000 ), nullable = False )
    desc = Column( Text )


plan_courseware_table = Table( 'anbels_logic_pan_courseware', metadata,
    Column( 'plan_id', Integer, ForeignKey( 'anbels_logic_plan.id',
        onupdate = "CASCADE", ondelete = "CASCADE" ), primary_key = True ),
    Column( 'courseware_id', Integer, ForeignKey( 'anbels_logic_courseware.id',
        onupdate = "CASCADE", ondelete = "CASCADE" ), primary_key = True ),
 )



class Plan( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_logic_plan'

    id = Column( Integer, autoincrement = True, primary_key = True )
    name = Column( Unicode( 1000 ), nullable = False )
    school_id = Column( Integer, ForeignKey( 'anbels_logic_school.id' ) )
    grade = Column( Integer, default = 1 )
    category_id = Column( Integer, ForeignKey( 'anbels_master_category.id' ) )
    coursewares = relation( 'Courseware', secondary = plan_courseware_table, backref = 'plans' )
    desc = Column( Text )



class Courseware( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_logic_courseware'

    id = Column( Integer, autoincrement = True, primary_key = True )
    name = Column( Unicode( 1000 ), nullable = False )
    category_id = Column( Integer, ForeignKey( 'anbels_master_category.id' ) )
    desc = Column( Text )
    path = Column( Unicode( 5000 ) )
    url = Column( Unicode( 5000 ) )
    type = Column( Unicode( 10 ) )  # G is game



class StudyLog( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_logic_study_log'

    id = Column( Integer, autoincrement = True, primary_key = True )
    user_id = Column( Integer, ForeignKey( 'anbels_auth_user.id' ) )
    user = relation( User )
    courseware_id = Column( Integer, ForeignKey( 'anbels_logic_courseware.id' ) )
    courseware = relation( Courseware )
    score = Column( Float )
    remark = Column( Text )


#===============================================================================
# system
#===============================================================================
class SystemLog( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_system_log'

    id = Column( Integer, autoincrement = True, primary_key = True )
    user_id = Column( Integer )
    remark = Column( Text )


class DataDictionary( DeclarativeBase ):
    __tablename__ = 'anbels_system_data_dictionary'

    id = Column( Integer, autoincrement = True, primary_key = True )
    name = Column( Unicode( 1000 ) )
    value = Column( Text )



def init():
    print "drop all tables"
    metadata.drop_all( engine, checkfirst = True )
    print "create table"
    metadata.create_all( engine )
    print "insert default value"

    admin = User( name = '10000000', password = '100' )
    teacher = User( name = '10000001', password = '101' )
    student = User( name = '10000002', password = '102' )
    DBSession.add_all( [ admin, teacher, student] )

    gAdmin = Group( name = 'ADMIN' , display_name = '超级管理员' )
    gAdmin.users = [admin, ]
    gTeacher = Group( name = 'TEACHER' , display_name = '老师' )
    gTeacher.users = [teacher, ]
    gStudent = Group( name = 'STUDENT' , display_name = '学生' )
    gStudent.users = [student, ]
    DBSession.add_all( [gAdmin, gTeacher, gStudent] )

    c1 = Category( name = u'消防教育' )
    c2 = Category( name = u'安全教育' )
    DBSession.add_all( [c1, c2, ] )
    DBSession.flush()

    cw1 = Courseware( name = u'课件一', category_id = c1.id )
    cw2 = Courseware( name = u'课件二', category_id = c1.id )
    cw3 = Courseware( name = u'课件三', category_id = c2.id )
    cw4 = Courseware( name = u'课件四', category_id = c2.id )

    DBSession.add_all( [cw1, cw2, cw3, cw4, ] )

    DBSession.commit()
    print "Done"


if __name__ == "__main__":
    init()
